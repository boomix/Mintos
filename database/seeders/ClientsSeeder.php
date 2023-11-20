<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Clients;
use App\Models\Accounts;
use App\Models\Currencies;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Clients::factory(10)->create()
            ->each(function (Clients $client) {

                $makeAccounts = rand(0, 3);

                if ($makeAccounts > 0) {
                    for ($i = 0; $i < $makeAccounts; $i++) {
                        // Add random amount of accounts with random balance
                        Accounts::create([
                            'client_id' => $client->id,
                            'balance' => rand(0, 100),
                            'currency_id' => Currencies::inRandomOrder()->first()->id
                        ]);
                    }
                }
            });
    }
}
