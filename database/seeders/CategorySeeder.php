<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $expenses = ['food', 'petrol', 'study', 'dating'];
        foreach ($expenses as $name) {
            Category::updateOrCreate(['name' => $name, 'type' => 'expense']);
        }

        $incomes = ['salary', 'gift', 'teaching'];
        foreach ($incomes as $name) {
            Category::updateOrCreate(['name' => $name, 'type' => 'income']);
        }
    }
}
