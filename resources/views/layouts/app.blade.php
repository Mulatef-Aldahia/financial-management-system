<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- جعل عنوان الصفحة ديناميكياً -->
    <title>@yield('title') - {{ \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'النظام المالي' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Cairo', sans-serif; background-color: #f3f4f6; }
        .glass-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05 ); }
        input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
</head>
<body class="flex h-screen overflow-hidden bg-gray-100">

    <!-- جلب اسم المتجر من قاعدة البيانات -->
    @php
        $appStoreName = \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'النظام المالي';
    @endphp

    <!-- القائمة الجانبية -->
    <aside class="w-64 bg-blue-900 text-white flex flex-col hidden md:flex">
        <div class="p-6 text-center border-b border-blue-800">
            <i class="fa-solid fa-wallet text-4xl mb-2"></i>
            <!-- عرض اسم المتجر الديناميكي -->
            <h2 class="text-xl font-bold">{{ $appStoreName }}</h2>
        </div>
        
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-800 font-bold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-chart-pie w-5"></i> لوحة التحكم
            </a>
            <a href="{{ route('daily-entry.create') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('daily-entry.create', 'daily-entry.edit') ? 'bg-blue-800 font-bold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-calendar-plus w-5"></i> إدخال يومية
            </a>
            <a href="{{ route('report') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('report') ? 'bg-blue-800 font-bold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-file-lines w-5"></i> التقارير الشهرية
            </a>
            
            <!-- رابط الإعدادات الجديد -->
            <a href="{{ route('settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors {{ request()->routeIs('settings') ? 'bg-blue-800 font-bold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}">
                <i class="fa-solid fa-gear w-5"></i> الإعدادات
            </a>
        </nav>

        <!-- تسجيل الخروج (تم نقله هنا ليكون أسفل القائمة الجانبية) -->
        <div class="p-4 border-t border-blue-800 mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-3 text-rose-300 hover:text-rose-100 px-4 py-2 transition-colors w-full font-bold">
                    <i class="fa-solid fa-right-from-bracket w-5"></i> تسجيل الخروج
                </button>
            </form>
        </div>
    </aside>

    <!-- المحتوى الرئيسي -->
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="bg-white shadow-sm p-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">@yield('title')</h1>
        </header>

        <div class="p-6">
            <!-- رسائل النجاح أو الخطأ -->
            @if(session('success'))
                <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative mb-4 font-bold shadow-sm">
                    <i class="fa-solid fa-check-circle ml-2"></i> {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-rose-100 border border-rose-400 text-rose-700 px-4 py-3 rounded relative mb-4 font-bold shadow-sm">
                    <div class="flex items-center mb-2"><i class="fa-solid fa-triangle-exclamation ml-2"></i> <span>يوجد خطأ:</span></div>
                    <ul class="list-disc list-inside pr-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
