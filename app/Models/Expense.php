<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['daily_revenue_id', 'amount', 'recipient', 'statement'];

    // علاقة: المصروف يتبع ليومية واحدة
    public function dailyRevenue()
    {
        return $this->belongsTo(DailyRevenue::class);
    }
}

