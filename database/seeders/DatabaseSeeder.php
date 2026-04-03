<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Market;
use App\Models\Outcome;
use App\Models\Bet;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $admin = User::create([
            'name' => 'The Boss',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'balance' => 5000000,
        ]);

        $traders = collect();
        for ($i = 0; $i < 25; $i++) {
            $traders->push(User::create([
                'name' => $faker->firstName . ' ' . substr($faker->lastName, 0, 1) . '.',
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'role' => 'user',
                'balance' => $faker->numberBetween(50000, 2000000),
            ]));
        }

        $marketData = [
            ['title' => ['en' => 'Will Bitcoin hit $100k before the end of 2026?', 'ro' => 'Va atinge Bitcoin pragul de 100.000$ până la finalul anului 2026?'], 'status' => 'active'],
            ['title' => ['en' => 'Will the Federal Reserve cut interest rates in Q3?', 'ro' => 'Va reduce Rezerva Federală ratele dobânzilor în T3?'], 'status' => 'resolved'],
            ['title' => ['en' => 'Will OpenAI release GPT-5 before December?', 'ro' => 'Va lansa OpenAI modelul GPT-5 înainte de luna decembrie?'], 'status' => 'archived'],
            
            ['title' => ['en' => 'Will Ethereum surpass Bitcoin in market cap by 2027?', 'ro' => 'Va depăși Ethereum capitalizarea de piață a Bitcoin până în 2027?'], 'status' => 'active'],
            ['title' => ['en' => 'Will Elon Musk step down as CEO of X (Twitter)?', 'ro' => 'Va renunța Elon Musk la funcția de CEO al X (Twitter)?'], 'status' => 'active'],
            ['title' => ['en' => 'Will TikTok be completely banned in the USA?', 'ro' => 'Va fi interzis complet TikTok în SUA?'], 'status' => 'active'],
            ['title' => ['en' => 'Will the S&P 500 reach 6000 points this year?', 'ro' => 'Va atinge S&P 500 pragul de 6000 de puncte anul acesta?'], 'status' => 'active'],
            ['title' => ['en' => 'Will Real Madrid win the Champions League again?', 'ro' => 'Va câștiga Real Madrid din nou Liga Campionilor?'], 'status' => 'active'],
            ['title' => ['en' => 'Will Avatar 3 gross over $2 billion worldwide?', 'ro' => 'Va depăși Avatar 3 încasări de 2 miliarde $ la nivel global?'], 'status' => 'active'],
            ['title' => ['en' => 'Will Apple launch AR/VR glasses under $1000?', 'ro' => 'Va lansa Apple ochelari AR/VR sub 1000$?'], 'status' => 'active'],
            ['title' => ['en' => 'Will humans walk on the Moon again by 2026?', 'ro' => 'Vor păși din nou oamenii pe Lună până în 2026?'], 'status' => 'active'],
            ['title' => ['en' => 'Will Amazon start accepting cryptocurrency payments?', 'ro' => 'Va începe Amazon să accepte plăți în criptomonede?'], 'status' => 'active'],
            ['title' => ['en' => 'Will Netflix acquire a major AAA game studio?', 'ro' => 'Va achiziționa Netflix un studio major de jocuri AAA?'], 'status' => 'active'],
            ['title' => ['en' => 'Will gold price hit $3000 per ounce?', 'ro' => 'Va atinge prețul aurului 3000$ pe uncie?'], 'status' => 'active'],
            ['title' => ['en' => 'Will MrBeast reach 400 million subscribers on YouTube?', 'ro' => 'Va atinge MrBeast 400 de milioane de abonați pe YouTube?'], 'status' => 'active'],
            ['title' => ['en' => 'Will GTA VI be released on PC at launch?', 'ro' => 'Va fi lansat GTA VI pe PC încă din prima zi?'], 'status' => 'active'],
            ['title' => ['en' => 'Will TikTok announce an IPO (go public)?', 'ro' => 'Va anunța TikTok o ofertă publică inițială (IPO)?'], 'status' => 'active'],
            ['title' => ['en' => 'Will Tesla announce a "Model 2" under $25,000?', 'ro' => 'Va anunța Tesla un "Model 2" sub 25.000$?'], 'status' => 'active'],
            ['title' => ['en' => 'Will Argentina win the Copa America?', 'ro' => 'Va câștiga Argentina Copa America?'], 'status' => 'resolved'],
            ['title' => ['en' => 'Will SpaceX successfully launch Starship without explosion?', 'ro' => 'Va lansa SpaceX cu succes Starship fără explozie?'], 'status' => 'resolved'],
            ['title' => ['en' => 'Will the US inflation drop below 2%?', 'ro' => 'Va scădea inflația din SUA sub 2%?'], 'status' => 'resolved'],
            ['title' => ['en' => 'Will a European country adopt Bitcoin as legal tender?', 'ro' => 'Va adopta o țară europeană Bitcoin ca monedă oficială?'], 'status' => 'resolved'],
            ['title' => ['en' => 'Will a human clone be born this year?', 'ro' => 'Se va naște o clonă umană anul acesta?'], 'status' => 'archived'],
            ['title' => ['en' => 'Will aliens make official contact?', 'ro' => 'Vor stabili extratereștrii un contact oficial?'], 'status' => 'archived'],
        ];

        foreach ($marketData as $data) {
            $market = Market::create([
                'title' => $data['title'],
                'status' => $data['status'],
                'total_pool' => 0,
            ]);

            $outcomeYes = Outcome::create([
                'market_id' => $market->id, 
                'name' => ['en' => 'YES', 'ro' => 'DA'], 
                'pool' => 0
            ]);
            
            $outcomeNo = Outcome::create([
                'market_id' => $market->id, 
                'name' => ['en' => 'NO', 'ro' => 'NU'], 
                'pool' => 0
            ]);

            $bettors = $traders->random(rand(8, 20));

            foreach ($bettors as $trader) {
                $trader->refresh();

                $betAmount = $faker->numberBetween(1000, 50000);

                if ($betAmount > $trader->balance) {
                    $betAmount = $trader->balance;
                }

                if ($betAmount <= 0) {
                    continue;
                }

                $isYesFavored = $faker->boolean(); 
                $chance = $isYesFavored ? 70 : 30;
                $chosenOutcome = $faker->boolean($chance) ? $outcomeYes : $outcomeNo; 

                $trader->balance -= $betAmount;
                $trader->save();

                Bet::create([
                    'user_id' => $trader->id,
                    'outcome_id' => $chosenOutcome->id,
                    'amount' => $betAmount,
                ]);

                $chosenOutcome->pool += $betAmount;
                $chosenOutcome->save();

                $market->total_pool += $betAmount;
                $market->save();
            }

            if ($market->status === 'resolved') {
                $winner = $faker->boolean() ? $outcomeYes : $outcomeNo;
                $winner->is_winner = 1;
                $winner->save();
            }
        }
    }
}