<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\Item;
use App\Models\Inventory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Generate some Users
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);
        User::factory(5)->create();

        // 2. Create Branches manually (since BranchFactory doesn't exist)
        $branches = collect([
            Branch::create(['id' => 'V5', 'name' => 'NXT V5', 'address' => 'Banga I, Plaridel']),
            Branch::create(['id' => 'V9', 'name' => 'NXT V9', 'address' => 'Sto Nino, Plaridel']),
        ]);

        // 3. Generate Items
        $items = Item::factory(20)->create();

        // 4. Create Inventory items for the branches
        // Initialize inventory for all items across all branches
        foreach ($branches as $branch) {
            foreach ($items as $item) {
                Inventory::factory()->create([
                    'item_id' => $item->id,
                    'branch_id' => $branch->id,
                ]);
            }
        }
    }
}
