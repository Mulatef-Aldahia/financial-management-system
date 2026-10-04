<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // عرض صفحة تسجيل الدخول
    public function showLoginForm()
    {
        // إذا كان المستخدم مسجل دخول بالفعل، حوله للوحة التحكم
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    // التحقق من البيانات وتسجيل الدخول
    public function login(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ], [
            'name.required' => 'اسم المستخدم مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ]);

        // بيانات الاعتماد
        $credentials = [
            'name' => $request->name,
            'password' => $request->password,
        ];

        // محاولة تسجيل الدخول مع تذكر الجلسة
        if (Auth::attempt($credentials, true)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'مرحباً بك مرة أخرى!');
        }

        return back()->withErrors([
            'name' => 'اسم المستخدم أو كلمة المرور غير صحيحة.',
        ])->withInput($request->except('password'));
    }

    // معالجة طلب التسجيل
    public function register(Request $request)
    {
        // التحقق من البيانات
        $request->validate([
            'name' => 'required|string|max:255|min:3|unique:users,name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'اسم المستخدم مطلوب',
            'name.min' => 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل',
            'name.unique' => 'اسم المستخدم مستخدم بالفعل',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'هذا البريد الإلكتروني مسجل مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'password.confirmed' => 'كلمتا المرور غير متطابقتين',
        ]);

        // إنشاء المستخدم الجديد
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // تسجيل الدخول تلقائياً
        Auth::login($user, true);

        return redirect()->route('dashboard')->with('success', 'تم إنشاء الحساب وتسجيل الدخول بنجاح!');
    }

    // تسجيل الخروج
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'تم تسجيل الخروج بنجاح');
    }
}