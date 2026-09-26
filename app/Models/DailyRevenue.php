<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRevenue extends Model
{
    protected $fillable = ['date', 'morning_shift', 'evening_shift', 'bank_feed', 'notes'];

    // علاقة: اليومية الواحدة تحتوي على عدة مصروفات
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}

