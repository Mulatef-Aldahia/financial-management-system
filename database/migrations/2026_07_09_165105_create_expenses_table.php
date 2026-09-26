<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::create('expenses', function (Blueprint $table) {
        $table->id();
        // ربط المصروف باليومية (إذا تم حذف اليومية تُحذف مصروفاتها تلقائياً)
        $table->foreignId('daily_revenue_id')->constrained()->onDelete('cascade'); 
        $table->decimal('amount', 10, 2); // المبلغ
        $table->string('recipient')->nullable(); // المستلم
        $table->string('statement'); // البيان / سبب الصرف
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
