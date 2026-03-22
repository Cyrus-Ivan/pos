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
            Branch::create(['code' => 'BR-001', 'name' => 'Main Branch', 'address' => '123 Main St']),
            Branch::create(['code' => 'BR-002', 'name' => 'Malls Branch', 'address' => '456 Mall Ave']),
            Branch::create(['code' => 'BR-003', 'name' => 'East Branch', 'address' => '789 East Blvd']),
        ]);

        // 3. Generate Items
        $items = Item::factory(20)->create();

        // 4. Create Inventory items for the branches
        // We override item_id and branch_id so it skips calling new Item/Branch factories
        foreach ($branches as $branch) {
            // Assign 10 random items to each branch to avoid unique constraint violations
            $branchItems = $items->random(10);

            foreach ($branchItems as $item) {
                Inventory::factory()->create([
                    'item_id' => $item->id,
                    'branch_id' => $branch->id,
                ]);
            }
        }
    }
}
