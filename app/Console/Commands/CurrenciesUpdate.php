<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Currencies;

class CurrenciesUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currencies:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency rates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $endpoint = 'live';
        $mainCurrency = 'USD';
        $access_key = '9f81857727e505327ed64ec4400a898a'; // Did not add to .env file so it is easier to test it

        $ch = curl_init('http://api.exchangerate.host/' . $endpoint . '?access_key=' . $access_key . '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);

        $exchangeRates = json_decode($json, true);

        if (isset($exchangeRates['quotes'])) {

            $data = [
                [
                    'currency' => $mainCurrency,
                    'rate' => 1.0
                ]
            ];
            foreach ($exchangeRates['quotes'] as $index => $rate) {
                $data[] = [
                    'currency' => str_replace($mainCurrency, '', $index),
                    'rate' => $rate
                ];
            }

            Currencies::upsert($data, ['currency']);
        }
    }
}
