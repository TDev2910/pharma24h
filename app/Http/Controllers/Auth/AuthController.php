<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ], 

        //thông báo lỗi xác thực
        [
            'email.required' => 'Trường email là bắt buộc.',
            'email.email' => 'Trường email phải là một địa chỉ email hợp lệ.',
            'password.required' => 'Trường mật khẩu là bắt buộc.',
            'password.min' => 'Trường mật khẩu phải có ít nhất 8 ký tự.',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            // QUAN TRỌNG: Regenerate session để tránh session fixation attacks
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // kiểm tra role trực tiếp
            if ($user->role === 'admin') {
                return redirect('/admin/admindashboard')->with('success', 'Chào mừng Admin!');
            }          
            
            // Redirect về Inertia route với Inertia response
            return redirect('/')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->withInput();
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {      
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'], 
            'email' => ['required', 'email', 'unique:users,email'], 
            'password' => ['required','string','confirmed','min:8'],
            'phone' => ['required','string','regex:/^[0-9]{10,11}$/'],
            'address' => ['nullable','string','max:255'],
        ], 
        
        //thông báo lỗi xác thực
        [
            'name.required' => 'Trường họ và tên là bắt buộc.',
            'email.required' => 'Trường email là bắt buộc.',
            'email.email' => 'Trường email phải là một địa chỉ email hợp lệ.',
            'email.unique' => 'Email đã được sử dụng.',
            'password.required' => 'Trường mật khẩu là bắt buộc.',
            'password.min' => 'Trường mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'phone.required' => 'Trường số điện thoại là bắt buộc.',
            'phone.regex' => 'Số điện thoại phải có 10-11 chữ số.',
            'address.string' => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max' => 'Địa chỉ không được quá 255 ký tự.',
        ]);

        // Tạo user mới với role mặc định là 'user'
        $userData = [
            'name' => $request->name, 
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'address' => $request->address, // Có thể null
            'role' => 'user', // Luôn mặc định là user
        ];
        
        $user = User::create($userData);
        Auth::login($user);   
        
        return redirect('/')->with('success', 'Đăng ký thành công! Chào mừng bạn đến với MediAid!');
    }
    
    /**
     * Handle logout request
     */
    public function logout()
    {
        Auth::logout(); 
        return redirect('/')->with('success', 'Đăng xuất thành công!');
    }
}



