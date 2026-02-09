<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use Illuminate\Database\Seeder;

class ChartOfAccountSeeder extends Seeder
{
    /**
     * Hotel-friendly chart of accounts. Uses firstOrCreate by code so safe to run multiple times.
     */
    public function run(): void
    {
        $accounts = [
            ['code' => 'CASH', 'name' => 'Cash', 'type' => ChartOfAccount::TYPE_ASSET, 'sort_order' => 10],
            ['code' => 'BANK', 'name' => 'Bank', 'type' => ChartOfAccount::TYPE_ASSET, 'sort_order' => 20],
            ['code' => 'AR', 'name' => 'Accounts Receivable', 'type' => ChartOfAccount::TYPE_ASSET, 'sort_order' => 30],
            ['code' => 'ROOM_REV', 'name' => 'Room Revenue', 'type' => ChartOfAccount::TYPE_REVENUE, 'sort_order' => 110],
            ['code' => 'POS_REV', 'name' => 'POS Revenue', 'type' => ChartOfAccount::TYPE_REVENUE, 'sort_order' => 120],
            ['code' => 'OTHER_REV', 'name' => 'Other Revenue', 'type' => ChartOfAccount::TYPE_REVENUE, 'sort_order' => 130],
            ['code' => 'SALARY_EXP', 'name' => 'Salary Expense', 'type' => ChartOfAccount::TYPE_EXPENSE, 'sort_order' => 210],
            ['code' => 'UTILITY_EXP', 'name' => 'Utility Expense', 'type' => ChartOfAccount::TYPE_EXPENSE, 'sort_order' => 220],
            ['code' => 'MAINT_EXP', 'name' => 'Maintenance Expense', 'type' => ChartOfAccount::TYPE_EXPENSE, 'sort_order' => 230],
            ['code' => 'OTHER_EXP', 'name' => 'Other Expense', 'type' => ChartOfAccount::TYPE_EXPENSE, 'sort_order' => 240],
        ];

        foreach ($accounts as $data) {
            ChartOfAccount::firstOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'is_active' => true,
                    'sort_order' => $data['sort_order'],
                ]
            );
        }
    }
}
