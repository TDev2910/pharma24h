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
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $user = Auth::user();
            
            // Sửa lại kiểm tra role trực tiếp
            if ($user->role === 'admin') {
                return redirect('/admin/admindashboard')->with('success', 'Chào mừng Admin!');
            }          
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
            'address' => ['required','string','max:255'],
            'province' => ['required','string'],
            'district' => ['required','string'],
            'ward' => ['required','string'],
        ]);

        // Tạo user mới với role mặc định là 'user'
        $userData = [
            'name' => $request->name, 
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'address' => $request->address,
            'province' => $request->province,
            'district' => $request->district,
            'ward' => $request->ward,
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



