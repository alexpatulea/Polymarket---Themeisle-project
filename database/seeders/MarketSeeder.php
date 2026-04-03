<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Market;

class MarketSeeder extends Seeder
{
    public function run(): void
    {
        // Primul Market în Engleză
        $market = Market::create([
            'title' => 'Will Bitcoin reach $150,000 by the end of 2026?',
            'status' => 'active',
        ]);

        $market->outcomes()->createMany([
            ['name' => 'Yes', 'pool' => 0],
            ['name' => 'No', 'pool' => 0],
        ]);
        
        // Al doilea Market în Engleză
        $market2 = Market::create([
            'title' => 'Who will win the Formula 1 championship?',
            'status' => 'active',
        ]);
        
        $market2->outcomes()->createMany([
            ['name' => 'Max Verstappen'],
            ['name' => 'Lewis Hamilton'],
            ['name' => 'Lando Norris'],
        ]);
    }
}