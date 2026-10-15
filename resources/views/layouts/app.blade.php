<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1e3a8a">
    <title>@yield('title') - {{ \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'النظام المالي' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { color-scheme: light; }
        body { font-family: 'Cairo', sans-serif; background-color: #f3f4f6; }
        .glass-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
        button, a, input, select, textarea { touch-action: manipulation; }
        @media (max-width: 767px) {
            .mobile-safe-area { padding-bottom: max(1rem, env(safe-area-inset-bottom)); }
            .mobile-page-padding { padding: 1rem; }
            .mobile-title { font-size: 1.25rem; }
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden bg-gray-100">
    @php
        $appStoreName = \App\Models\Setting::where('key', 'store_name')->value('value') ?? 'النظام المالي';
    @endphp

    <!-- القائمة الجانبية لسطح المكتب -->
    <aside class="no-print fixed inset-y-0 right-0 z-30 hidden w-64 flex-col bg-blue-900 text-white md:flex">
        <div class="border-b border-blue-800 p-6 text-center">
            <i class="fa-solid fa-wallet mb-2 text-4xl"></i>
            <h2 class="break-words text-xl font-bold">{{ $appStoreName }}</h2>
        </div>
        <nav class="flex-1 space-y-2 overflow-y-auto p-4">
            <a href="{{ route('dashboard') }}" class="flex min-h-12 items-center gap-3 rounded-lg px-4 py-3 transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-800 font-bold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}"><i class="fa-solid fa-chart-pie w-5"></i> لوحة التحكم</a>
            <a href="{{ route('daily-entry.create') }}" class="flex min-h-12 items-center gap-3 rounded-lg px-4 py-3 transition-colors {{ request()->routeIs('daily-entry.create', 'daily-entry.edit') ? 'bg-blue-800 font-bold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}"><i class="fa-solid fa-calendar-plus w-5"></i> إدخال يومية</a>
            <a href="{{ route('report') }}" class="flex min-h-12 items-center gap-3 rounded-lg px-4 py-3 transition-colors {{ request()->routeIs('report') ? 'bg-blue-800 font-bold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}"><i class="fa-solid fa-file-lines w-5"></i> التقارير الشهرية</a>
            <a href="{{ route('settings') }}" class="flex min-h-12 items-center gap-3 rounded-lg px-4 py-3 transition-colors {{ request()->routeIs('settings') ? 'bg-blue-800 font-bold' : 'text-blue-200 hover:bg-blue-800 hover:text-white' }}"><i class="fa-solid fa-gear w-5"></i> الإعدادات</a>
        </nav>
        <div class="border-t border-blue-800 p-4">
            <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="flex min-h-12 w-full items-center gap-3 px-4 py-2 font-bold text-rose-300 hover:text-rose-100"><i class="fa-solid fa-right-from-bracket w-5"></i> تسجيل الخروج</button></form>
        </div>
    </aside>

    <div id="mobileMenu" class="no-print fixed inset-0 z-40 hidden md:hidden">
        <button type="button" onclick="toggleMobileMenu()" class="absolute inset-0 bg-slate-900/50" aria-label="إغلاق القائمة"></button>
        <aside class="relative flex h-full w-[min(19rem,85vw)] flex-col bg-blue-900 text-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-blue-800 p-5">
                <h2 class="break-words text-lg font-bold">{{ $appStoreName }}</h2>
                <button type="button" onclick="toggleMobileMenu()" class="min-h-11 min-w-11 rounded-lg text-2xl text-blue-100" aria-label="إغلاق القائمة"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <nav class="space-y-2 p-4">
                <a onclick="toggleMobileMenu()" href="{{ route('dashboard') }}" class="flex min-h-12 items-center gap-3 rounded-lg px-4 py-3 {{ request()->routeIs('dashboard') ? 'bg-blue-800 font-bold' : 'text-blue-200' }}"><i class="fa-solid fa-chart-pie w-5"></i> لوحة التحكم</a>
                <a onclick="toggleMobileMenu()" href="{{ route('daily-entry.create') }}" class="flex min-h-12 items-center gap-3 rounded-lg px-4 py-3 {{ request()->routeIs('daily-entry.create', 'daily-entry.edit') ? 'bg-blue-800 font-bold' : 'text-blue-200' }}"><i class="fa-solid fa-calendar-plus w-5"></i> إدخال يومية</a>
                <a onclick="toggleMobileMenu()" href="{{ route('report') }}" class="flex min-h-12 items-center gap-3 rounded-lg px-4 py-3 {{ request()->routeIs('report') ? 'bg-blue-800 font-bold' : 'text-blue-200' }}"><i class="fa-solid fa-file-lines w-5"></i> التقارير الشهرية</a>
                <a onclick="toggleMobileMenu()" href="{{ route('settings') }}" class="flex min-h-12 items-center gap-3 rounded-lg px-4 py-3 {{ request()->routeIs('settings') ? 'bg-blue-800 font-bold' : 'text-blue-200' }}"><i class="fa-solid fa-gear w-5"></i> الإعدادات</a>
            </nav>
            <div class="mt-auto border-t border-blue-800 p-4 mobile-safe-area"><form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="flex min-h-12 w-full items-center gap-3 px-4 py-2 font-bold text-rose-300"><i class="fa-solid fa-right-from-bracket w-5"></i> تسجيل الخروج</button></form></div>
        </aside>
    </div>

    <main class="min-h-screen md:mr-64">
        <header class="no-print sticky top-0 z-20 flex min-h-16 items-center justify-between gap-3 bg-white px-4 py-3 shadow-sm md:px-6">
            <button type="button" onclick="toggleMobileMenu()" class="flex min-h-11 min-w-11 items-center justify-center rounded-lg bg-blue-50 text-xl text-blue-900 md:hidden" aria-label="فتح القائمة"><i class="fa-solid fa-bars"></i></button>
            <h1 class="mobile-title min-w-0 flex-1 truncate text-xl font-bold text-gray-800 md:text-2xl">@yield('title')</h1>
            <span class="hidden max-w-[35%] truncate text-sm font-semibold text-gray-500 sm:block">{{ $appStoreName }}</span>
        </header>
        <div class="mobile-page-padding p-6">
            @if(session('success'))
                <div class="mb-4 rounded-lg border border-emerald-400 bg-emerald-100 px-4 py-3 font-bold text-emerald-700"><i class="fa-solid fa-check-circle ml-2"></i> {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-lg border border-rose-400 bg-rose-100 px-4 py-3 font-bold text-rose-700"><div class="mb-2 flex items-center"><i class="fa-solid fa-triangle-exclamation ml-2"></i> <span>يوجد خطأ:</span></div><ul class="list-inside list-disc pr-4">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @yield('content')
        </div>
    </main>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }
    </script>
    @stack('scripts')
</body>
</html>
