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

            //xử lý clear session cũ trước
            session()->forget(['phone_verification', 'phone_for_verification']);

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
        $phone = $request->query('phone') ?: session('phone_for_verification');

        // Debug: Log thông tin phone
        \Log::info('Phone verification debug', [
            'query_phone' => $request->query('phone'),
            'session_phone' => session('phone_for_verification'),
            'final_phone' => $phone,
            'all_query_params' => $request->query()
        ]);

        if (!$phone) {
            return redirect()->route('password.request')
                ->withErrors(['phone' => 'Số điện thoại không hợp lệ']);
        }

        // Lưu phone vào session để tránh mất khi redirect
        session(['phone_for_verification' => $phone]);

        return view('auth.passwords.verify-phone', compact('phone'));
    }

    /**
     * Xử lý xác thực OTP qua phone (Firebase)
     * Nhận uid/idToken nếu OTP đúng, hoặc otp nếu OTP sai để đếm attempts
     */
    public function verifyPhoneOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'uid' => 'nullable|string',
            'idToken' => 'nullable|string',
            'otp' => 'nullable|string|size:6'
        ]);

        $phone = $request->phone; // Nhận: "+84376193244"
        $uid = $request->uid;
        $idToken = $request->idToken;
        $otp = $request->otp;

        // ✅ Chuyển đổi từ +84376193244 thành 0376193244
        $normalizedPhone = $this->normalizePhoneForDatabase($phone);

        // ✅ Tìm user với số điện thoại đã chuẩn hóa
        $user = User::where('phone', $normalizedPhone)->first();

        if (!$user) {
            // Debug log để kiểm tra
            \Log::info('Phone verification failed', [
                'original_phone' => $phone,
                'normalized_phone' => $normalizedPhone,
                'all_users_phones' => User::pluck('phone')->toArray()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số điện thoại chưa được đăng ký tài khoản'
                ], 404);
            }

            return back()->withErrors(['phone' => 'Số điện thoại chưa được đăng ký tài khoản']);
        }

        // ✅ Nếu có uid và idToken -> OTP đúng (đã verify với Firebase thành công)
        if ($uid && $idToken) {
            // Xóa attempts counter và cache cũ
            $otpKey = "phone_otp_attempts_{$phone}";
            Cache::forget($otpKey);

            // Clear session cũ trước
            session()->forget(['reset_email', 'phone_verification']);

            // Lưu thông tin vào session
            session([
                'phone_verification' => [
                    'phone' => $phone,
                    'uid' => $uid,
                    'idToken' => $idToken,
                    'verified' => true,
                    'user_id' => $user->id
                ],
                // ✅ Lưu email của user vào reset_email
                'reset_email' => $user->email
            ]);

            // Handle AJAX request - Kiểm tra header để đảm bảo trả về JSON
            if (
                $request->ajax() ||
                $request->wantsJson() ||
                $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                $request->header('Accept') === 'application/json' ||
                str_contains($request->header('Accept', ''), 'application/json')
            ) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xác thực thành công!',
                    'redirect_url' => route('password.reset')
                ]);
            }

            return redirect()->route('password.reset')
                ->with('success', 'Xác thực thành công! Vui lòng đặt mật khẩu mới.');
        }

        // ✅ Nếu không có uid/idToken nhưng có otp -> OTP sai, cần đếm attempts
        if ($otp) {
            $otpKey = "phone_otp_attempts_{$phone}";

            // Lấy số lần thử hiện tại từ cache
            $otpData = Cache::get($otpKey, [
                'attempts' => 0,
                'created_at' => now()
            ]);

            // Tăng số lần thử
            $otpData['attempts']++;

            // Kiểm tra số lần thử
            if ($otpData['attempts'] >= 3) {
                // Xóa cache và yêu cầu gửi lại OTP
                Cache::forget($otpKey);

                $errorMessage = 'Bạn đã nhập sai quá 3 lần. Vui lòng yêu cầu mã OTP mới!';

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'max_attempts_reached' => true
                    ], 422);
                }

                return back()->withErrors(['otp' => $errorMessage]);
            }

            // Lưu lại số lần thử vào cache (5 phút)
            Cache::put($otpKey, $otpData, 300);

            $remainingAttempts = 3 - $otpData['attempts'];
            $errorMessage = "Mã OTP không đúng. Còn {$remainingAttempts} lần thử!";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'remaining_attempts' => $remainingAttempts
                ], 422);
            }

            return back()->withErrors(['otp' => $errorMessage]);
        }

        // Nếu không có cả uid/idToken và otp -> Lỗi request
        $errorMessage = 'Thiếu thông tin xác thực. Vui lòng thử lại!';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 422);
        }

        return back()->withErrors(['otp' => $errorMessage]);
    }

    private function normalizePhoneForDatabase($phone)
    {
        // Loại bỏ tất cả ký tự không phải số
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Nếu bắt đầu bằng 84 (từ +84), loại bỏ 84 và thêm 0
        if (substr($cleaned, 0, 2) === '84') {
            $cleaned = '0' . substr($cleaned, 2);
        }

        // Nếu không bắt đầu bằng 0, thêm 0
        if (substr($cleaned, 0, 1) !== '0') {
            $cleaned = '0' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Lưu phone vào session với rate limiting
     */
    public function savePhoneToSession(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $phone = $request->phone;
        $rateLimitKey = "phone_otp_rate_limit_{$phone}";

        // Kiểm tra rate limiting (3 lần trong 1 giờ)
        if (Cache::has($rateLimitKey)) {
            $attempts = Cache::get($rateLimitKey);
            if ($attempts >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quá nhiều yêu cầu. Vui lòng thử lại sau 1 giờ.',
                    'rate_limited' => true
                ], 429);
            }
        }

        // Tăng số lần thử
        $attempts = Cache::get($rateLimitKey, 0) + 1;
        Cache::put($rateLimitKey, $attempts, 3600); // 1 giờ

        session()->forget(['reset_email', 'phone_verification']);

        // Lưu phone vào session
        session(['phone_for_verification' => $phone]);

        return response()->json([
            'success' => true,
            'message' => 'Phone saved to session',
            'attempts_remaining' => 3 - $attempts
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Phone saved to session',
            'attempts_remaining' => 3 - $attempts
        ]);
    }

    /**
     * Reset attempts counter khi gửi lại OTP
     */
    public function resetPhoneOtpAttempts(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $phone = $request->phone;
        $otpKey = "phone_otp_attempts_{$phone}";

        // Xóa attempts counter
        Cache::forget($otpKey);

        return response()->json([
            'success' => true,
            'message' => 'Đã reset số lần thử'
        ]);
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
            // ✅ SỬA: Chuẩn hóa số điện thoại
            $normalizedPhone = $this->normalizePhoneForDatabase($request->phone);

            // Tìm user theo phone number đã chuẩn hóa
            $user = User::where('phone', $normalizedPhone)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số điện thoại chưa được đăng ký tài khoản'
                ], 404);
            }

            // Clear session cũ trước
            session()->forget(['reset_email', 'phone_verification']);

            // Lưu thông tin xác thực vào session
            session([
                'phone_verification' => [
                    'phone' => $request->phone,
                    'uid' => $request->uid,
                    'idToken' => $request->idToken,
                    'verified' => true,
                    'user_id' => $user->id
                ],
                // ✅ THÊM: Lưu email của user vào reset_email
                'reset_email' => $user->email
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

        // ✅ THÊM: Debug logging
        \Log::info('Reset form debug', [
            'reset_email' => $email,
            'phone_verification' => $phoneVerification,
            'all_session' => session()->all()
        ]);

        //xử lý email và xác thực số điện thoại
        if (!$email && !$phoneVerification) {
            return redirect()->route('password.request')->withErrors(['email' => 'Phiên làm việc đã hết hạn. Vui lòng thử lại!']);
        }

        if (!$email && $phoneVerification && isset($phoneVerification['user_id'])) {
            $user = User::find($phoneVerification['user_id']);
            if ($user) {
                $email = $user->email;
                session(['reset_email' => $email]);
            }
        }

        if ($phoneVerification && !isset($phoneVerification['verified'])) {
            return redirect()->route('password.request')->withErrors(['phone' => 'Xác thực phone chưa hoàn thành. Vui lòng thử lại!']);
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
