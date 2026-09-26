@extends('layouts.app')
@section('title', 'إعدادات النظام')
@section('content')
@if(session('success'))
    <div class="mb-6 bg-emerald-100 text-emerald-700 p-4 rounded-lg font-bold">✓ {{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="mb-6 bg-rose-100 text-rose-700 p-4 rounded-lg font-bold">✗ {{ $errors->first() }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div class="glass-card p-6 border-t-4 border-blue-900">
        <h3 class="text-xl font-black text-blue-900 mb-6">بيانات النظام</h3>
        <form action="{{ route('settings.store-name') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label class="block font-bold mb-2">اسم النظام</label>
                <input type="text" name="store_name" value="{{ $storeName }}" class="w-full border rounded-lg px-4 py-3" required>
            </div>
            <button type="submit" class="bg-blue-900 text-white font-bold py-2 px-6 rounded-lg">حفظ الاسم</button>
        </form>
    </div>

    <div class="glass-card p-6 border-t-4 border-rose-500">
        <h3 class="text-xl font-black text-rose-700 mb-6">تغيير كلمة المرور</h3>
        <form action="{{ route('settings.password') }}" method="POST">
            @csrf
            <div class="mb-4"><label class="block font-bold mb-2">الحالية</label><input type="password" name="current_password" class="w-full border rounded-lg px-4 py-3" required></div>
            <div class="mb-4"><label class="block font-bold mb-2">الجديدة</label><input type="password" name="new_password" class="w-full border rounded-lg px-4 py-3" required></div>
            <div class="mb-5"><label class="block font-bold mb-2">تأكيد الجديدة</label><input type="password" name="new_password_confirmation" class="w-full border rounded-lg px-4 py-3" required></div>
            <button type="submit" class="bg-rose-600 text-white font-bold py-2 px-6 rounded-lg">تحديث</button>
        </form>
    </div>
</div>
@endsection
