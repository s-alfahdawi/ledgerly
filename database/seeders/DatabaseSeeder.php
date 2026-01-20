<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Wallet;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        if (app()->environment('local')) {
            $user = User::factory()->create([
                'name' => 'Demo User',
                'email' => 'demo@example.com',
            ]);

            $account = Account::create([
                'name' => 'Personal',
                'currency_code' => 'USD',
                'timezone' => 'UTC',
                'owner_user_id' => $user->id,
            ]);

            $account->users()->attach($user->id, [
                'role' => 'owner',
                'joined_at' => now(),
            ]);

            $categories = [
                ['name' => 'Salary', 'type' => 'income'],
                ['name' => 'Freelance', 'type' => 'income'],
                ['name' => 'Food', 'type' => 'expense'],
                ['name' => 'Transport', 'type' => 'expense'],
                ['name' => 'Rent', 'type' => 'expense'],
            ];

            foreach ($categories as $cat) {
                Category::create(array_merge($cat, ['account_id' => $account->id]));
            }

            Wallet::create([
                'account_id' => $account->id,
                'name' => 'Cash',
                'type' => 'cash',
                'opening_balance' => 0,
            ]);

            Wallet::create([
                'account_id' => $account->id,
                'name' => 'Bank',
                'type' => 'bank',
                'opening_balance' => 0,
            ]);
        }
    }
}
