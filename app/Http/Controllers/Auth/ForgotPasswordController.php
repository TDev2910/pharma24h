<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /**
     * Hiển thị form nhập email để quên mật khẩu
     */
    public function showEmailForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Xử lý gửi OTP đến email
     */
    public function sendOtp(Request $request)
    {
        try {
            // Validate email
            $request->validate([
                'email' => 'required|email|exists:users,email'
            ], [
                'email.required' => 'Vui lòng nhập địa chỉ email',
                'email.email' => 'Địa chỉ email không hợp lệ',
                'email.exists' => 'Email này chưa được đăng ký tài khoản'
            ]);

            $email = $request->email;
            
            // Kiểm tra cooldown (không cho gửi liên tục)
            $cooldownKey = "otp_cooldown_{$email}";
            if (Cache::has($cooldownKey)) {
                return back()->withErrors(['email' => 'Vui lòng đợi 60 giây trước khi gửi lại OTP']);
            }

            // Tạo OTP 5 số
            $otp = str_pad(random_int(10000, 99999), 5, '0', STR_PAD_LEFT);
            
            // Lưu OTP vào cache (5 phút)
            $otpKey = "otp_{$email}";
            Cache::put($otpKey, [
                'code' => $otp,
                'attempts' => 0,
                'created_at' => now()
            ], 300); // 5 phút
            
            // Set cooldown 60 giây
            Cache::put($cooldownKey, true, 60);

            // Gửi email
            Mail::raw("Mã OTP của bạn là: {$otp}\n\nMã có hiệu lực trong 5 phút.\n\nTrân trọng,\nPharma24h", function ($message) use ($email) {
                $message->to($email)
                ->subject('Mã OTP đặt lại mật khẩu - Pharma24h');
            });

            // Lưu email vào session thay vì URL parameter
            session(['otp_email' => $email]);
            
            return redirect()->route('password.verify')
                           ->with('success', 'Mã OTP đã được gửi đến email của bạn!');

        } catch (\Exception $e) {
            // Debug: Log chi tiết lỗi
            \Log::error('Forgot Password Error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Lỗi gửi OTP: ' . $e->getMessage()]);
        }
    }

    /**
     * Hiển thị form nhập OTP
     */
    public function showVerifyForm(Request $request)
    {
        $email = session('otp_email');
        
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Phiên làm việc đã hết hạn. Vui lòng thử lại!']);
        }

        return view('auth.passwords.verify', compact('email'));
    }

    /**
     * Xác thực OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:5'
        ]);

        $email = $request->email;
        $inputOtp = $request->otp;
        $otpKey = "otp_{$email}";

        // Lấy OTP từ cache
        $otpData = Cache::get($otpKey);
        
        // Debug: Log thông tin OTP
        \Log::info('OTP Verification Debug', [
            'email' => $email,
            'input_otp' => $inputOtp,
            'cache_key' => $otpKey,
            'otp_data_exists' => !is_null($otpData),
            'otp_data' => $otpData
        ]);
        
        if (!$otpData) {
            return back()->withErrors(['otp' => 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới!']);
        }

        // Kiểm tra số lần thử
        if ($otpData['attempts'] >= 3) {
            Cache::forget($otpKey);
            return back()->withErrors(['otp' => 'Bạn đã nhập sai quá 3 lần. Vui lòng yêu cầu mã OTP mới!']);
        }

        // Kiểm tra OTP
        if ($inputOtp !== $otpData['code']) {
            // Tăng số lần thử
            $otpData['attempts']++;
            Cache::put($otpKey, $otpData, 300);
            
            $remainingAttempts = 3 - $otpData['attempts'];
            return back()->withErrors(['otp' => "Mã OTP không đúng. Còn {$remainingAttempts} lần thử!"]);
        }

                // OTP đúng - xóa khỏi cache và lưu email vào session
        Cache::forget($otpKey);
        session(['reset_email' => $email]);
        
        return redirect()->route('password.reset')
                       ->with('success', 'Xác thực thành công! Vui lòng đặt mật khẩu mới.');
    }

    /**
     * Hiển thị form xác thực OTP qua phone
     */
    public function showPhoneVerifyForm(Request $request)
    {
        $phone = $request->query('phone');
        
        if (!$phone) {
            return redirect()->route('password.request')
                ->withErrors(['phone' => 'Số điện thoại không hợp lệ']);
        }

        return view('auth.passwords.verify-phone', compact('phone'));
    }

    /**
     * Xử lý xác thực OTP qua phone (Firebase)
     */
    public function verifyPhoneOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string|size:6'
        ]);

        $phone = $request->phone;
        $otp = $request->otp;

        // Lưu thông tin vào session để backend xử lý
        session([
            'phone_verification' => [
                'phone' => $phone,
                'otp' => $otp,
                'verified' => false
            ]
        ]);

        return redirect()->route('password.reset')
             ->with('success', 'Xác thực thành công! Vui lòng đặt mật khẩu mới.');
    }

    /**
     * Xử lý xác thực phone từ Firebase (AJAX)
     */
    public function handlePhoneVerification(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'uid' => 'required|string',
            'idToken' => 'required|string'
        ]);

        try {
            // Tìm user theo phone number
            $user = User::where('phone', $request->phone)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số điện thoại chưa được đăng ký tài khoản'
                ], 404);
            }

            // Lưu thông tin xác thực vào session
            session([
                'phone_verification' => [
                    'phone' => $request->phone,
                    'uid' => $request->uid,
                    'idToken' => $request->idToken,
                    'verified' => true,
                    'user_id' => $user->id
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Xác thực thành công',
                'redirect_url' => route('password.reset')
            ]);

        } catch (\Exception $e) {
            \Log::error('Phone verification error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra, vui lòng thử lại'
            ], 500);
        }
    }

    /**
     * Hiển thị form đặt lại mật khẩu
     */
    public function showResetForm(Request $request)
    {
        $email = session('reset_email');
        $phoneVerification = session('phone_verification');
        
        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Phiên làm việc đã hết hạn. Vui lòng thử lại!']);
        }

        return view('auth.passwords.reset', compact('email'));
    }

    /**
     * Xử lý đặt lại mật khẩu
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp'
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            $user->password = bcrypt($request->password);
            $user->save();

            return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập.');

        } catch (\Exception $e) {
            return back()->withErrors(['password' => 'Có lỗi xảy ra. Vui lòng thử lại!']);
        }
    }
}
