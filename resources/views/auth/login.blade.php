<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - النظام المالي</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-blue-900 to-blue-700">
    
    <div class="bg-white p-10 rounded-2xl shadow-2xl w-full max-w-md">
        <div class="text-center mb-8">
            <div class="bg-blue-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i class="fa-solid fa-vault text-4xl text-blue-900"></i>
            </div>
            <h1 class="text-2xl font-black text-gray-800">النظام المالي الاحترافي</h1>
            <p class="text-gray-500 mt-2 font-bold">تسجيل الدخول للمتابعة</p>
        </div>

        @if($errors->any( ))
            <div class="bg-rose-100 text-rose-700 p-3 rounded-lg mb-6 text-sm font-bold text-center border border-rose-200">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf
                <div class="mb-5">
                <label class="block text-gray-700 font-bold mb-2">اسم المستخدم</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute right-4 top-3.5 text-gray-400"></i>
                    <input type="text" name="username" class="w-full border border-gray-300 rounded-lg pr-10 pl-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-900 font-bold" required value="{{ old('username') }}" dir="ltr" placeholder="admin">
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
    </div>

</body>
</html>
