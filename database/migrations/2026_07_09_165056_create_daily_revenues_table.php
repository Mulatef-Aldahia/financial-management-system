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
         Schema::create('daily_revenues', function (Blueprint $table) {
        $table->id();
        $table->date('date')->unique(); // تاريخ اليوم (يجب أن يكون فريداً لكل يوم)
        $table->decimal('morning_shift', 10, 2)->default(0); // الشفت الصباحي
        $table->decimal('evening_shift', 10, 2)->default(0); // الشفت المسائي
        $table->decimal('bank_feed', 10, 2)->default(0); // تغذية البنك
        $table->text('notes')->nullable(); // ملاحظات
        $table->timestamps();
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_revenues');
    }
};
