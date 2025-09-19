<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item; // <-- Import the Item model

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // We can update items by their name or ID
        Item::where('name', 'ata')->update(['price' => 55.50]);
        Item::where('name', 'oil')->update(['price' => 120]);
        Item::where('name', 'shirt')->update(['price' => 500]);
        Item::where('name', 'thumbsup')->update(['price' => 40]);
        Item::where('id', 17)->update(['price' => 75]); // Updating dosa batter by ID
    }
}