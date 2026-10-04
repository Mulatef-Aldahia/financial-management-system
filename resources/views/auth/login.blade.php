<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>النظام المالي الاحترافي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Cairo', sans-serif; }
        .fade-in { animation: fadeIn 0.4s ease-in-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-900 to-blue-700 p-4">
    
    <div class="bg-white p-8 md:p-10 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <div class="bg-blue-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i class="fa-solid fa-vault text-4xl text-blue-900"></i>
            </div>
            <h1 class="text-2xl font-black text-gray-800">النظام المالي الاحترافي</h1>
            <p id="subtitle" class="text-gray-500 mt-2 font-bold">تسجيل الدخول للمتابعة</p>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 text-emerald-700 p-3 rounded-lg mb-6 text-sm font-bold text-center border border-emerald-200 fade-in">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-100 text-rose-700 p-3 rounded-lg mb-6 text-sm font-bold text-center border border-rose-200 fade-in">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <!-- ================= نموذج تسجيل الدخول ================= -->
        <div id="loginForm" class="fade-in">
            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="mb-5">
                    <label class="block text-gray-700 font-bold mb-2">اسم المستخدم</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute right-4 top-3.5 text-gray-400"></i>
                        <!-- تم تغيير name من username إلى name ليتطابق مع الـ Controller -->
                        <input type="text" name="name" class="w-full border border-gray-300 rounded-lg pr-10 pl-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-900 font-bold" required value="{{ old('name') }}" dir="ltr" placeholder="أدخل اسم المستخدم">
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-700 font-bold mb-2">كلمة المرور</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute right-4 top-3.5 text-gray-400"></i>
                        <input type="password" name="password" class="w-full border border-gray-300 rounded-lg pr-10 pl-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-900 font-bold" required dir="ltr" placeholder="••••••••">
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-blue-900 text-white font-black text-lg py-3 rounded-lg hover:bg-blue-800 transition-colors shadow-lg">
                    دخول <i class="fa-solid fa-arrow-right-to-bracket ml-2"></i>
                </button>
            </form>

            <div class="text-center mt-6 pt-6 border-t border-gray-200">
                <p class="text-gray-600 mb-2">مستخدم جديد؟</p>
                <button onclick="toggleForms()" class="text-blue-900 font-black hover:text-blue-700 transition-colors flex items-center justify-center gap-2 mx-auto">
                    <i class="fa-solid fa-user-plus"></i> إنشاء حساب جديد
                </button>
            </div>
        </div>

        <!-- ================= نموذج إنشاء حساب جديد ================= -->
        <div id="registerForm" class="hidden fade-in">
            <form action="{{ route('register') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">البريد الإلكتروني</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute right-4 top-3.5 text-gray-400"></i>
                        <input type="email" name="email" class="w-full border border-gray-300 rounded-lg pr-10 pl-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-900 font-bold" required value="{{ old('email') }}" dir="ltr" placeholder="example@domain.com">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">اسم المستخدم</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute right-4 top-3.5 text-gray-400"></i>
                        <input type="text" name="name" class="w-full border border-gray-300 rounded-lg pr-10 pl-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-900 font-bold" required value="{{ old('name') }}" dir="ltr" placeholder="أدخل اسم المستخدم">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">كلمة المرور</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute right-4 top-3.5 text-gray-400"></i>
                        <input type="password" name="password" class="w-full border border-gray-300 rounded-lg pr-10 pl-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-900 font-bold" required dir="ltr" placeholder="••••••••">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-2">تأكيد كلمة المرور</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute right-4 top-3.5 text-gray-400"></i>
                        <input type="password" name="password_confirmation" class="w-full border border-gray-300 rounded-lg pr-10 pl-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-900 font-bold" required dir="ltr" placeholder="••••••••">
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-emerald-600 text-white font-black text-lg py-3 rounded-lg hover:bg-emerald-700 transition-colors shadow-lg">
                    إنشاء حساب والدخول <i class="fa-solid fa-user-check ml-2"></i>
                </button>
            </form>

            <div class="text-center mt-6 pt-6 border-t border-gray-200">
                <p class="text-gray-600 mb-2">لديك حساب بالفعل؟</p>
                <button onclick="toggleForms()" class="text-blue-900 font-black hover:text-blue-700 transition-colors flex items-center justify-center gap-2 mx-auto">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> تسجيل الدخول
                </button>
            </div>
        </div>

    </div>

    <!-- سكربت التبديل بين النموذجين -->
    <script>
        function toggleForms() {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const subtitle = document.getElementById('subtitle');
            
            if (loginForm.classList.contains('hidden')) {
                // إظهار تسجيل الدخول
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                subtitle.textContent = 'تسجيل الدخول للمتابعة';
            } else {
                // إظهار التسجيل
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                subtitle.textContent = 'إنشاء حساب جديد';
            }
        }
    </script>
</body>
</html>