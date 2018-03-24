<?php

namespace App\Console\Commands;

use App\Currency;
use App\Repositories\UserRepository;
use Illuminate\Console\Command;

class FetchCurrencies extends Command
{
    protected $signature = 'fetch:currencies';
    protected $description = 'Fetching currencies for users accounts';


    public function handle()
    {
        $currencies = UserRepository::getUsersCurrencies();

        $bar = $this->output->createProgressBar($currencies->count());

        foreach ($currencies as $currency)
        {
            try
            {
                $rate = \Swap::latest($currency . '/' . Currency::DEFAULT_CURRENCY);

                Currency::query()->updateOrCreate([
                    'date'     => $rate->getDate()->format('Y-m-d'),
                    'currency' => $currency,
                ], [
                    'rate'=> $rate->getValue(),
                ]);
            }
            catch (\Exception $exception)
            {
                $this->error($exception->getMessage());
            }
            $bar->advance();
        }
        $bar->finish();
    }
}
