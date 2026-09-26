@extends('layouts.app')
@section('title', 'لوحة التحكم')

@section('content')
@php
    // حساب الإجماليات من قاعدة البيانات (هذا الجزء للـ PHP فقط)
    $totalRevenueMonth = $entries->sum(fn($e) => $e->morning_shift + $e->evening_shift + $e->bank_feed);
    $totalExpenseMonth = $entries->sum(fn($e) => $e->expenses->sum('amount'));
    $currentBalance = $openingBalance + $totalRevenueMonth - $totalExpenseMonth;

    // تجهيز بيانات الرسم البياني (آخر 7 أيام)
    $chartData = $entries->take(7)->reverse();
    $labels = $chartData->pluck('date')->toArray();
    $revenues = $chartData->map(fn($e) => $e->morning_shift + $e->evening_shift + $e->bank_feed)->toArray();
    $expenses = $chartData->map(fn($e) => $e->expenses->sum('amount'))->toArray();
@endphp

<!-- إعداد الرصيد الافتتاحي (هنا يبدأ الـ HTML) -->
<div class="mb-6 glass-card p-4 flex flex-col md:flex-row items-center justify-between gap-4 bg-blue-50 border border-blue-200">
    <div class="flex items-center gap-3 text-blue-900 font-bold">
        <i class="fa-solid fa-vault text-2xl"></i>
        <span class="text-lg">الرصيد الافتتاحي للصندوق:</span>
    </div>
    <form action="{{ route('update-balance') }}" method="POST" class="flex items-center gap-2">
        @csrf
        <input type="number" name="balance" value="{{ $openingBalance }}" step="0.01" class="border border-gray-300 rounded-lg px-4 py-2 w-40 text-center font-bold focus:ring-2 focus:ring-blue-900">
        <button type="submit" class="bg-blue-900 text-white px-6 py-2 rounded-lg hover:bg-blue-800 font-bold transition-colors">
            تحديث الرصيد
        </button>
    </form>
</div>

<div class="space-y-6">
    <!-- البطاقات الإحصائية -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="glass-card p-6 border-r-4 border-blue-900 relative overflow-hidden">
            <h3 class="text-gray-500 font-bold text-sm mb-1">رصيد الصندوق الفعلي</h3>
            <div class="text-3xl font-black text-blue-900">{{ number_format($currentBalance, 2) }} <span class="text-sm font-normal text-gray-500">د.ك</span></div>
        </div>
        <div class="glass-card p-6 border-r-4 border-emerald-500 relative overflow-hidden">
            <h3 class="text-gray-500 font-bold text-sm mb-1">إجمالي الوارد</h3>
            <div class="text-3xl font-black text-emerald-600">{{ number_format($totalRevenueMonth, 2) }} <span class="text-sm font-normal text-gray-500">د.ك</span></div>
        </div>
        <div class="glass-card p-6 border-r-4 border-rose-500 relative overflow-hidden">
            <h3 class="text-gray-500 font-bold text-sm mb-1">إجمالي المنصرف</h3>
            <div class="text-3xl font-black text-rose-600">{{ number_format($totalExpenseMonth, 2) }} <span class="text-sm font-normal text-gray-500">د.ك</span></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- الرسم البياني -->
        <div class="glass-card p-6 lg:col-span-2">
            <h3 class="text-lg font-bold text-gray-700 mb-4"><i class="fa-solid fa-chart-line ml-2 text-blue-900"></i>حركة الإيرادات والمصروفات</h3>
            <div class="relative h-72 w-full"><canvas id="financeChart"></canvas></div>
        </div>

        <!-- السجل اليومي -->
        <div class="glass-card p-6 lg:col-span-1 flex flex-col">
            <h3 class="text-lg font-bold text-gray-700 mb-4"><i class="fa-solid fa-clock-rotate-left ml-2 text-blue-900"></i>السجل اليومي</h3>
            <div class="overflow-x-auto flex-grow">
                <table class="w-full text-sm text-right">
                    <thead class="text-xs text-gray-500 bg-gray-50 rounded-lg">
                        <tr><th class="px-3 py-2">التاريخ</th><th class="px-3 py-2">الصافي</th><th class="px-3 py-2 text-center">إجراء</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($entries as $entry)
                            @php
                                $dayRev = $entry->morning_shift + $entry->evening_shift + $entry->bank_feed;
                                $dayExp = $entry->expenses->sum('amount');
                                $net = $dayRev - $dayExp;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-3 font-bold text-gray-700">{{ $entry->date }}</td>
                                <td class="px-3 py-3 font-bold {{ $net >= 0 ? 'text-emerald-600' : 'text-rose-600' }}" dir="ltr">
                                    {{ $net > 0 ? '+' : '' }}{{ number_format($net, 2) }}
                                </td>
                                <td class="px-3 py-3 text-center flex justify-center gap-2">
                                    <a href="{{ route('daily-entry.edit', $entry->id) }}" class="text-blue-500 bg-blue-50 p-1.5 rounded hover:bg-blue-100"><i class="fa-solid fa-pen"></i></a>
                                    <form action="{{ route('daily-entry.destroy', $entry->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف يومية {{ $entry->date }}؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-500 bg-rose-50 p-1.5 rounded hover:bg-rose-100"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4 text-gray-500">لا توجد حركات مسجلة بعد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const ctx = document.getElementById('financeChart').getContext('2d');
    Chart.defaults.font.family = "'Cairo', sans-serif";
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [
                { label: 'إجمالي الوارد', data: @json($revenues), backgroundColor: 'rgba(16, 185, 129, 0.8)', borderRadius: 4 },
                { label: 'إجمالي المنصرف', data: @json($expenses), backgroundColor: 'rgba(244, 63, 94, 0.8)', borderRadius: 4 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { rtl: true } } }
    });
</script>
@endpush
@endsection
