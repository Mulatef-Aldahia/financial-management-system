@extends('layouts.app')
@section('title', 'التقرير الشهري')

@section('content')
<div class="glass-card mb-6 border border-blue-200 bg-blue-50 p-4 md:p-6">
    <form action="{{ route('report') }}" method="GET" class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div class="grid grid-cols-2 gap-3 sm:flex sm:flex-wrap sm:items-end">
            <label class="col-span-2 text-sm font-bold text-blue-900 sm:col-span-1 sm:self-center">اختر الشهر والتقرير:</label>
            <div><span class="mb-1 block text-xs font-semibold text-gray-600">الشهر</span><select name="month" class="min-h-12 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 font-bold focus:border-blue-700 focus:ring-2 focus:ring-blue-200">@for($i = 1; $i <= 12; $i++)<option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $month == $i ? 'selected' : '' }}>شهر {{ $i }}</option>@endfor</select></div>
            <div><span class="mb-1 block text-xs font-semibold text-gray-600">السنة</span><select name="year" class="min-h-12 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 font-bold focus:border-blue-700 focus:ring-2 focus:ring-blue-200">@for($i = 2024; $i <= 2030; $i++)<option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>@endfor</select></div>
            <button type="submit" class="col-span-2 min-h-12 rounded-lg bg-blue-900 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-blue-800 sm:col-span-1"><i class="fa-solid fa-eye ml-1"></i> عرض التقرير</button>
        </div>
        <button type="button" onclick="window.print()" class="min-h-12 rounded-lg bg-emerald-600 px-5 py-3 font-bold text-white shadow-sm transition hover:bg-emerald-700"><i class="fa-solid fa-print ml-1"></i> طباعة التقرير</button>
    </form>
</div>

@php
    $totalMonthRev = 0;
    $totalMonthExp = 0;
@endphp

<!-- نسخة الجوال: بطاقة مستقلة لكل يوم بدلاً من تمرير جدول عريض -->
<div class="space-y-4 md:hidden" id="mobile-report">
    <div class="glass-card border border-yellow-200 bg-yellow-50 p-4">
        <div class="text-sm font-bold text-gray-600">رصيد بداية الشهر</div>
        <div class="mt-1 text-2xl font-black text-blue-900" dir="ltr">{{ number_format($monthOpeningBalance, 2) }} د.ك</div>
    </div>
    @forelse($entries as $entry)
        @php
            $dayRev = $entry->morning_shift + $entry->evening_shift + $entry->bank_feed;
            $dayExp = $entry->expenses->sum('amount');
            $dayNet = $dayRev - $dayExp;
            $totalMonthRev += $dayRev;
            $totalMonthExp += $dayExp;
        @endphp
        <article class="glass-card overflow-hidden border border-gray-200">
            <div class="flex items-center justify-between bg-blue-900 px-4 py-3 text-white">
                <div class="font-black"><i class="fa-solid fa-calendar-day ml-2"></i>{{ $entry->date }}</div>
                <div class="text-sm font-bold" dir="ltr">صافي {{ number_format($dayNet, 2) }} د.ك</div>
            </div>
            <div class="grid grid-cols-2 gap-3 p-4 text-sm">
                <div class="rounded-lg bg-emerald-50 p-3"><div class="text-gray-600">إجمالي الوارد</div><strong class="text-lg text-emerald-700" dir="ltr">{{ number_format($dayRev, 2) }}</strong></div>
                <div class="rounded-lg bg-rose-50 p-3"><div class="text-gray-600">إجمالي المنصرف</div><strong class="text-lg text-rose-700" dir="ltr">{{ number_format($dayExp, 2) }}</strong></div>
                <div class="rounded-lg bg-gray-50 p-3"><div class="text-gray-600">الشفت الصباحي</div><strong dir="ltr">{{ number_format($entry->morning_shift, 2) }}</strong></div>
                <div class="rounded-lg bg-gray-50 p-3"><div class="text-gray-600">الشفت المسائي</div><strong dir="ltr">{{ number_format($entry->evening_shift, 2) }}</strong></div>
                <div class="col-span-2 rounded-lg bg-gray-50 p-3"><div class="text-gray-600">تغذية البنك</div><strong dir="ltr">{{ number_format($entry->bank_feed, 2) }}</strong></div>
            </div>
            @if($entry->expenses->count())
                <div class="border-t border-gray-100 px-4 pb-4 pt-3"><h3 class="mb-2 font-bold text-rose-700">تفاصيل المنصرف</h3><div class="space-y-2">@foreach($entry->expenses as $expense)<div class="flex items-start justify-between gap-3 rounded-lg bg-rose-50 p-3 text-sm"><div class="min-w-0"><div class="font-bold">{{ $expense->statement ?: 'مصروف' }}</div><div class="text-gray-600">{{ $expense->recipient ?: 'غير محدد' }}</div></div><strong class="shrink-0 text-rose-700" dir="ltr">{{ number_format($expense->amount, 2) }}</strong></div>@endforeach</div></div>
            @endif
        </article>
    @empty
        <div class="glass-card p-8 text-center text-gray-500"><i class="fa-solid fa-calendar-xmark mb-2 text-3xl"></i><p>لا توجد حركات مسجلة لهذا الشهر.</p></div>
    @endforelse
    <div class="glass-card border border-blue-200 bg-blue-900 p-4 text-white">
        <div class="flex items-center justify-between"><span class="font-bold">الرصيد الفعلي نهاية الشهر</span><strong class="text-xl" dir="ltr">{{ number_format($monthOpeningBalance + $totalMonthRev - $totalMonthExp, 2) }} د.ك</strong></div>
        <div class="mt-3 grid grid-cols-2 gap-2 border-t border-blue-700 pt-3 text-sm"><span>الوارد: <b dir="ltr">{{ number_format($totalMonthRev, 2) }}</b></span><span>المنصرف: <b dir="ltr">{{ number_format($totalMonthExp, 2) }}</b></span></div>
    </div>
</div>

<!-- نسخة سطح المكتب والطباعة: تخطيط Excel الكامل -->
<div class="glass-card hidden overflow-x-auto p-4 md:block md:p-6" id="printable-area">
    <h2 class="mb-4 text-center text-2xl font-black text-blue-900">تقرير حركة الصندوق - شهر {{ $month }} / {{ $year }}</h2>
    <table class="w-full min-w-[900px] border-collapse border border-gray-300 text-center text-sm">
        <thead><tr class="bg-blue-900 text-white"><th class="border border-gray-300 p-2" colspan="3">حركة المنصرف</th><th class="border border-gray-300 p-2" rowspan="2">التاريخ</th><th class="border border-gray-300 p-2" colspan="4">حركة الوارد</th></tr><tr class="bg-gray-100 font-bold text-gray-800"><th class="border border-gray-300 p-2 text-rose-600">المبلغ</th><th class="border border-gray-300 p-2">المستلم</th><th class="border border-gray-300 p-2">البيان</th><th class="border border-gray-300 p-2 text-emerald-600">الإجمالي</th><th class="border border-gray-300 p-2">تغذية بنك</th><th class="border border-gray-300 p-2">شفت مسائي</th><th class="border border-gray-300 p-2">شفت صباحي</th></tr></thead>
        <tbody>
            <tr class="bg-yellow-100 font-bold"><td class="border border-gray-300 p-2" colspan="3"></td><td class="border border-gray-300 p-2 text-blue-900">رصيد بداية الشهر</td><td class="border border-gray-300 p-2 text-blue-900" colspan="4">{{ number_format($monthOpeningBalance, 2) }}</td></tr>
            @php $totalMonthRev = 0; $totalMonthExp = 0; @endphp
            @foreach($entries as $entry)
                @php $dayRev = $entry->morning_shift + $entry->evening_shift + $entry->bank_feed; $totalMonthRev += $dayRev; $expensesCount = max(1, $entry->expenses->count()); @endphp
                @for($i = 0; $i < $expensesCount; $i++)
                    @php $exp = $entry->expenses[$i] ?? null; if($exp) $totalMonthExp += $exp->amount; @endphp
                    <tr class="hover:bg-gray-50"><td class="border border-gray-300 p-2 font-bold text-rose-600">{{ $exp ? number_format($exp->amount, 2) : '' }}</td><td class="border border-gray-300 p-2">{{ $exp ? $exp->recipient : '' }}</td><td class="border border-gray-300 p-2">{{ $exp ? $exp->statement : '' }}</td>@if($i == 0)<td class="border border-gray-300 p-2 font-bold" rowspan="{{ $expensesCount }}">{{ $entry->date }}</td><td class="border border-gray-300 bg-gray-50 p-2 font-bold text-emerald-600" rowspan="{{ $expensesCount }}">{{ number_format($dayRev, 2) }}</td><td class="border border-gray-300 p-2" rowspan="{{ $expensesCount }}">{{ $entry->bank_feed > 0 ? number_format($entry->bank_feed, 2) : '' }}</td><td class="border border-gray-300 p-2" rowspan="{{ $expensesCount }}">{{ number_format($entry->evening_shift, 2) }}</td><td class="border border-gray-300 p-2" rowspan="{{ $expensesCount }}">{{ number_format($entry->morning_shift, 2) }}</td>@endif</tr>
                @endfor
            @endforeach
            <tr class="bg-gray-200 text-lg font-black"><td class="border border-gray-300 p-3 text-rose-600">{{ number_format($totalMonthExp, 2) }}</td><td class="border border-gray-300 p-3" colspan="2">إجمالي المنصرف</td><td class="border border-gray-300 p-3"></td><td class="border border-gray-300 p-3 text-emerald-600">{{ number_format($totalMonthRev, 2) }}</td><td class="border border-gray-300 p-3" colspan="3">إجمالي الوارد</td></tr>
            <tr class="bg-blue-900 text-xl font-black text-white"><td class="border border-gray-300 p-4" colspan="4">الرصيد الفعلي في الصندوق نهاية الشهر</td><td class="border border-gray-300 p-4" colspan="4" dir="ltr">{{ number_format($monthOpeningBalance + $totalMonthRev - $totalMonthExp, 2) }} د.ك</td></tr>
        </tbody>
    </table>
</div>

<style>
    @media print {
        body { background: white !important; }
        body * { visibility: hidden; }
        #printable-area, #printable-area * { visibility: visible; }
        #printable-area { display: block !important; position: absolute; left: 0; top: 0; width: 100%; padding: 0; }
        .glass-card { box-shadow: none; border: none; }
    }
</style>
@endsection
