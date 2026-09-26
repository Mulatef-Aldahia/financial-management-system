<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyRevenue;
use App\Models\Expense;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    public function dashboard()
    {
        $entries = DailyRevenue::with('expenses')->orderBy('date', 'desc')->get();
        $openingBalance = Setting::where('key', 'opening_balance')->value('value') ?? 0;
        return view('dashboard', compact('entries', 'openingBalance'));
    }

    public function updateBalance(Request $request)
    {
        $request->validate(['balance' => 'required|numeric']);
        Setting::updateOrCreate(['key' => 'opening_balance'], ['value' => $request->balance]);
        return redirect()->back()->with('success', 'تم تحديث الرصيد الافتتاحي بنجاح!');
    }

    public function create()
    {
        $entries = DailyRevenue::with('expenses')->orderBy('date', 'desc')->get();
        return view('daily-entry', compact('entries'));
    }
    // حفظ يومية جديدة
    public function store(Request $request)
    {
        $existingEntry = DailyRevenue::where('date', $request->date)->first();
        if ($existingEntry) {
            return redirect()->route('daily-entry.edit', $existingEntry->id)
                             ->with('success', 'هذا اليوم مسجل مسبقاً! تم فتح وضع التعديل.');
        }

        $request->validate([
            'date' => 'required|date|unique:daily_revenues,date',
            'morning_shift' => 'required|numeric',
            'evening_shift' => 'required|numeric',
        ]);
        
        DB::transaction(function () use ($request) {
            $daily = DailyRevenue::create([
                'date' => $request->date,
                'morning_shift' => $request->morning_shift,
                'evening_shift' => $request->evening_shift,
                'bank_feed' => $request->bank_feed ?? 0,
                'notes' => $request->notes,
            ]);

            if ($request->has('expenses')) {
                foreach ($request->expenses as $exp) {
                    if (isset($exp['amount']) && is_numeric($exp['amount'])) {
                        $daily->expenses()->create([
                            'amount' => $exp['amount'],
                            // الحل هنا: إذا كان الحقل فارغاً، احفظ علامة "-"
                            'recipient' => $exp['recipient'] ?? '-',
                            'statement' => $exp['statement'] ?? '-',
                        ]);
                    }
                }
            }
        });
        return redirect()->route('daily-entry.create')->with('success', 'تم الحفظ بنجاح!');
    }

    public function edit($id)
    {
        $entry = DailyRevenue::with('expenses')->findOrFail($id);
        $entries = DailyRevenue::with('expenses')->orderBy('date', 'desc')->get();
        return view('daily-entry', compact('entry', 'entries'));
    }
    
    // تحديث يومية موجودة
    public function update(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date|unique:daily_revenues,date,'.$id,
            'morning_shift' => 'required|numeric',
            'evening_shift' => 'required|numeric',
        ]);
        
        DB::transaction(function () use ($request, $id) {
            $daily = DailyRevenue::findOrFail($id);
            $daily->update([
                'date' => $request->date,
                'morning_shift' => $request->morning_shift,
                'evening_shift' => $request->evening_shift,
                'bank_feed' => $request->bank_feed ?? 0,
                'notes' => $request->notes,
            ]);
            
            $daily->expenses()->delete(); 
            if ($request->has('expenses')) {
                foreach ($request->expenses as $exp) {
                    if (isset($exp['amount']) && is_numeric($exp['amount'])) {
                        $daily->expenses()->create([
                            'amount' => $exp['amount'],
                            // الحل هنا أيضاً
                            'recipient' => $exp['recipient'] ?? '-',
                            'statement' => $exp['statement'] ?? '-',
                        ]);
                    }
                }
            }
        });
        return redirect()->route('daily-entry.create')->with('success', 'تم التعديل بنجاح!');
    }

    public function destroy($id)
    {
        DailyRevenue::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'تم الحذف بنجاح!');
    }

    // ==========================================
    // دالة التقارير الشهرية (الجديدة)
    // ==========================================
    public function report(Request $request)
    {
        // تحديد الشهر والسنة (الافتراضي هو الشهر الحالي)
        $month = $request->month ?? date('m');
        $year = $request->year ?? date('Y');

        // جلب بيانات هذا الشهر فقط
        $entries = DailyRevenue::with('expenses')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'asc')
            ->get();

        // حساب "رصيد بداية المدة" لهذا الشهر
        // (الرصيد الافتتاحي للنظام + كل الإيرادات السابقة - كل المصروفات السابقة)
        $systemOpeningBalance = Setting::where('key', 'opening_balance')->value('value') ?? 0;
        
        $previousRevenues = DailyRevenue::where('date', '<', "$year-$month-01")
            ->sum(DB::raw('morning_shift + evening_shift + bank_feed'));
            
        $previousExpenses = Expense::whereHas('dailyRevenue', function($q) use ($year, $month) {
            $q->where('date', '<', "$year-$month-01");
        })->sum('amount');

        $monthOpeningBalance = $systemOpeningBalance + $previousRevenues - $previousExpenses;

        return view('report', compact('entries', 'monthOpeningBalance', 'month', 'year'));
    }
}
