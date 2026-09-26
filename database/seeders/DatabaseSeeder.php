<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إنشاء حساب المدير العام باستخدام اسم المستخدم
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'المدير العام',
                'password' => Hash::make('123456')
            ]
        );
    }
}
