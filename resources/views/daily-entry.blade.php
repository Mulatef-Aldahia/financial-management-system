@extends('layouts.app')
@section('title', isset($entry) ? 'تعديل حركة اليوم' : 'إدخال حركة اليوم')

@section('content')
@php
    $isEdit = isset($entry);
    $actionUrl = $isEdit ? route('daily-entry.update', $entry->id) : route('daily-entry.store');
@endphp

<!-- نموذج الإدخال / التعديل -->
<form action="{{ $actionUrl }}" method="POST" id="dailyForm">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="mb-6 flex items-center gap-3 bg-white p-4 rounded-xl shadow-sm border-r-4 border-blue-900 w-fit">
        <label class="font-semibold text-gray-700">تاريخ الحركة:</label>
        <input type="date" name="date" class="border border-gray-300 rounded-lg px-4 py-2" required value="{{ $isEdit ? $entry->date : old('date', date('Y-m-d')) }}">
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- الإيرادات -->
        <div class="glass-card p-6 border-t-4 border-emerald-500">
            <h3 class="text-lg font-bold text-emerald-700 mb-5">حركة الوارد</h3>
            <div class="space-y-4">
                <!-- الشفت الصباحي (إلزامي) -->
                <div><label class="block text-sm font-semibold mb-1">الشفت الصباحي <span class="text-rose-500">*</span></label><input type="number" name="morning_shift" class="w-full border border-gray-300 rounded-lg px-4 py-2" step="0.01" required value="{{ $isEdit ? $entry->morning_shift : old('morning_shift') }}"></div>
                
                <!-- الشفت المسائي (إلزامي) -->
                <div><label class="block text-sm font-semibold mb-1">الشفت المسائي <span class="text-rose-500">*</span></label><input type="number" name="evening_shift" class="w-full border border-gray-300 rounded-lg px-4 py-2" step="0.01" required value="{{ $isEdit ? $entry->evening_shift : old('evening_shift') }}"></div>
                
                <!-- تغذية البنك (اختياري) -->
                <div><label class="block text-sm font-semibold mb-1">تغذية من البنك</label><input type="number" name="bank_feed" class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-emerald-50" step="0.01" value="{{ $isEdit ? $entry->bank_feed : old('bank_feed') }}"></div>
                
                <!-- ملاحظات (اختياري) -->
                <div><label class="block text-sm font-semibold mb-1">ملاحظات</label><textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2">{{ $isEdit ? $entry->notes : old('notes') }}</textarea></div>
            </div>
        </div>

        <!-- المصروفات -->
        <div class="glass-card p-6 border-t-4 border-rose-500">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-lg font-bold text-rose-700">حركة المنصرف</h3>
                <button type="button" onclick="addExpenseRow()" class="bg-rose-100 text-rose-700 hover:bg-rose-200 px-3 py-1.5 rounded-md text-sm font-bold"><i class="fa-solid fa-plus"></i> إضافة مصروف</button>
            </div>
            <div id="expenses-container" class="space-y-3">
                @if($isEdit && $entry->expenses->count() > 0)
                    @foreach($entry->expenses as $index => $exp)
                        <div class="expense-row grid grid-cols-12 gap-2 items-center bg-gray-50 p-2 rounded-lg border border-gray-100">
                            <!-- مبلغ المصروف (إلزامي) -->
                            <div class="col-span-3"><input type="number" name="expenses[{{$index}}][amount]" value="{{ $exp->amount }}" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm" step="0.01" required placeholder="المبلغ *"></div>
                            <div class="col-span-4"><input type="text" name="expenses[{{$index}}][recipient]" value="{{ $exp->recipient }}" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm" placeholder="المستلم"></div>
                            <div class="col-span-4"><input type="text" name="expenses[{{$index}}][statement]" value="{{ $exp->statement }}" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm" placeholder="البيان"></div>
                            <div class="col-span-1 text-center"><button type="button" onclick="this.closest('.expense-row').remove()" class="text-gray-400 hover:text-rose-500"><i class="fa-solid fa-trash"></i></button></div>
                        </div>
                    @endforeach
                @else
                    <div class="expense-row grid grid-cols-12 gap-2 items-center bg-gray-50 p-2 rounded-lg border border-gray-100">
                        <!-- مبلغ المصروف (إلزامي) -->
                        <div class="col-span-3"><input type="number" name="expenses[0][amount]" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm" placeholder="المبلغ *" step="0.01" required></div>
                        <div class="col-span-4"><input type="text" name="expenses[0][recipient]" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm" placeholder="المستلم"></div>
                        <div class="col-span-4"><input type="text" name="expenses[0][statement]" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm" placeholder="البيان"></div>
                        <div class="col-span-1 text-center"><button type="button" onclick="this.closest('.expense-row').remove()" class="text-gray-400 hover:text-rose-500"><i class="fa-solid fa-trash"></i></button></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-6 glass-card p-6 flex justify-end border-t-4 border-blue-900">
        @if($isEdit)
            <a href="{{ route('daily-entry.create') }}" class="bg-gray-500 text-white py-3 px-6 rounded-xl ml-4 font-bold">إلغاء التعديل</a>
        @endif
        <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white font-bold py-3 px-10 rounded-xl shadow-lg transition-colors">
            <i class="fa-solid fa-floppy-disk"></i> {{ $isEdit ? 'حفظ التعديلات' : 'حفظ بيانات اليوم' }}
        </button>
    </div>
</form>

<!-- عرض السجل تحت الإدخال مباشرة -->
<div class="mt-8 glass-card p-6">
    <h3 class="text-lg font-bold text-gray-700 mb-4"><i class="fa-solid fa-clock-rotate-left text-blue-900"></i> السجل اليومي</h3>
    <table class="w-full text-sm text-right">
        <thead class="bg-gray-100 text-gray-600">
            <tr><th class="p-3">التاريخ</th><th class="p-3">الإيراد</th><th class="p-3">المنصرف</th><th class="p-3">الصافي</th><th class="p-3 text-center">إجراء</th></tr>
        </thead>
        <tbody>
            @foreach($entries as $e)
                @php
                    $rev = $e->morning_shift + $e->evening_shift + $e->bank_feed;
                    $exp = $e->expenses->sum('amount');
                    $net = $rev - $exp;
                @endphp
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3 font-bold">{{ $e->date }}</td>
                    <td class="p-3 text-emerald-600">{{ number_format($rev, 2) }}</td>
                    <td class="p-3 text-rose-600">{{ number_format($exp, 2) }}</td>
                    <td class="p-3 font-bold {{ $net >= 0 ? 'text-emerald-600' : 'text-rose-600' }}" dir="ltr">{{ $net > 0 ? '+' : '' }}{{ number_format($net, 2) }}</td>
                    <td class="p-3 text-center flex justify-center gap-2">
                        <a href="{{ route('daily-entry.edit', $e->id) }}" class="bg-blue-100 text-blue-600 p-2 rounded hover:bg-blue-200" title="تعديل"><i class="fa-solid fa-pen"></i></a>
                        <form action="{{ route('daily-entry.destroy', $e->id) }}" method="POST" onsubmit="return confirm('تأكيد الحذف؟');">
                            @csrf @method('DELETE')
                            <button class="bg-rose-100 text-rose-600 p-2 rounded hover:bg-rose-200" title="حذف"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- كود الجافاسكربت لإضافة صفوف المصروفات -->
@push('scripts')
<script>
    let expenseIndex = 100; 
    function addExpenseRow() {
        const container = document.getElementById('expenses-container');
        const rowHTML = `
            <div class="expense-row grid grid-cols-12 gap-2 items-center bg-gray-50 p-2 rounded-lg border border-gray-100 mt-2">
                <!-- مبلغ المصروف (إلزامي) -->
                <div class="col-span-3"><input type="number" name="expenses[${expenseIndex}][amount]" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm" placeholder="المبلغ *" step="0.01" required></div>
                <div class="col-span-4"><input type="text" name="expenses[${expenseIndex}][recipient]" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm" placeholder="المستلم"></div>
                <div class="col-span-4"><input type="text" name="expenses[${expenseIndex}][statement]" class="w-full border border-gray-300 rounded-md px-2 py-1.5 text-sm" placeholder="البيان"></div>
                <div class="col-span-1 text-center"><button type="button" onclick="this.closest('.expense-row').remove()" class="text-gray-400 hover:text-rose-500"><i class="fa-solid fa-trash"></i></button></div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', rowHTML);
        expenseIndex++;
    }
</script>
@endpush
@endsection
