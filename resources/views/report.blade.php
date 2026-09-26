@extends('layouts.app')
@section('title', 'التقرير الشهري')

@section('content')
<div class="glass-card p-6 mb-6 flex justify-between items-center bg-blue-50 border border-blue-200">
    <form action="{{ route('report') }}" method="GET" class="flex items-center gap-4">
        <label class="font-bold text-blue-900">اختر الشهر:</label>
        <select name="month" class="border border-gray-300 rounded-lg px-4 py-2 font-bold">
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}" {{ $month == $i ? 'selected' : '' }}>
                    شهر {{ $i }}
                </option>
            @endfor
        </select>
        <select name="year" class="border border-gray-300 rounded-lg px-4 py-2 font-bold">
            @for($i = 2024; $i <= 2030; $i++)
                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        <button type="submit" class="bg-blue-900 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-800">عرض التقرير</button>
    </form>
    <button onclick="window.print()" class="bg-emerald-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-emerald-700">
        <i class="fa-solid fa-print"></i> طباعة التقرير
    </button>
</div>

<div class="glass-card p-6 overflow-x-auto" id="printable-area">
    <h2 class="text-center text-2xl font-black text-blue-900 mb-4">تقرير حركة الصندوق - شهر {{ $month }} / {{ $year }}</h2>
    
    <table class="w-full text-sm text-center border-collapse border border-gray-300">
        <thead>
            <tr class="bg-blue-900 text-white">
                <th class="border border-gray-300 p-2" colspan="3">حركة المنصرف</th>
                <th class="border border-gray-300 p-2" rowspan="2">التاريخ</th>
                <th class="border border-gray-300 p-2" colspan="4">حركة الوارد</th>
            </tr>
            <tr class="bg-gray-100 text-gray-800 font-bold">
                <th class="border border-gray-300 p-2 text-rose-600">المبلغ</th>
                <th class="border border-gray-300 p-2">المستلم</th>
                <th class="border border-gray-300 p-2">البيان</th>
                <th class="border border-gray-300 p-2 text-emerald-600">الإجمالي</th>
                <th class="border border-gray-300 p-2">تغذية بنك</th>
                <th class="border border-gray-300 p-2">شفت مسائي</th>
                <th class="border border-gray-300 p-2">شفت صباحي</th>
            </tr>
        </thead>
        <tbody>
            <!-- رصيد بداية المدة -->
            <tr class="bg-yellow-100 font-bold">
                <td class="border border-gray-300 p-2" colspan="3"></td>
                <td class="border border-gray-300 p-2 text-blue-900">رصيد بداية الشهر</td>
                <td class="border border-gray-300 p-2 text-blue-900" colspan="4">{{ number_format($monthOpeningBalance, 2) }}</td>
            </tr>

            @php
                $totalMonthRev = 0;
                $totalMonthExp = 0;
            @endphp

            @foreach($entries as $entry)
                @php
                    $dayRev = $entry->morning_shift + $entry->evening_shift + $entry->bank_feed;
                    $totalMonthRev += $dayRev;
                    $expensesCount = max(1, $entry->expenses->count());
                @endphp

                @for($i = 0; $i < $expensesCount; $i++)
                    @php
                        $exp = $entry->expenses[$i] ?? null;
                        if($exp) $totalMonthExp += $exp->amount;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="border border-gray-300 p-2 text-rose-600 font-bold">{{ $exp ? number_format($exp->amount, 2) : '' }}</td>
                        <td class="border border-gray-300 p-2">{{ $exp ? $exp->recipient : '' }}</td>
                        <td class="border border-gray-300 p-2">{{ $exp ? $exp->statement : '' }}</td>
                        
                        @if($i == 0)
                            <td class="border border-gray-300 p-2 font-bold" rowspan="{{ $expensesCount }}">{{ $entry->date }}</td>
                            <td class="border border-gray-300 p-2 text-emerald-600 font-bold bg-gray-50" rowspan="{{ $expensesCount }}">{{ number_format($dayRev, 2) }}</td>
                            <td class="border border-gray-300 p-2" rowspan="{{ $expensesCount }}">{{ $entry->bank_feed > 0 ? number_format($entry->bank_feed, 2) : '' }}</td>
                            <td class="border border-gray-300 p-2" rowspan="{{ $expensesCount }}">{{ number_format($entry->evening_shift, 2) }}</td>
                            <td class="border border-gray-300 p-2" rowspan="{{ $expensesCount }}">{{ number_format($entry->morning_shift, 2) }}</td>
                        @endif
                    </tr>
                @endfor
            @endforeach

            <!-- الإجماليات النهائية -->
            <tr class="bg-gray-200 font-black text-lg">
                <td class="border border-gray-300 p-3 text-rose-600">{{ number_format($totalMonthExp, 2) }}</td>
                <td class="border border-gray-300 p-3" colspan="2">إجمالي المنصرف</td>
                <td class="border border-gray-300 p-3"></td>
                <td class="border border-gray-300 p-3 text-emerald-600">{{ number_format($totalMonthRev, 2) }}</td>
                <td class="border border-gray-300 p-3" colspan="3">إجمالي الوارد</td>
            </tr>
            <tr class="bg-blue-900 text-white font-black text-xl">
                <td class="border border-gray-300 p-4" colspan="4">الرصيد الفعلي في الصندوق نهاية الشهر</td>
                <td class="border border-gray-300 p-4" colspan="4" dir="ltr">{{ number_format($monthOpeningBalance + $totalMonthRev - $totalMonthExp, 2) }} د.ك</td>
            </tr>
        </tbody>
    </table>
</div>

<style>
    @media print {
        body * { visibility: hidden; }
        #printable-area, #printable-area * { visibility: visible; }
        #printable-area { position: absolute; left: 0; top: 0; width: 100%; }
        .glass-card { box-shadow: none; border: none; }
    }
</style>
@endsection
