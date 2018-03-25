<?php

namespace App\Console\Commands;

use App\Currency;
use App\Repositories\UserRepository;
use App\Services\CurrencyCollector;
use Illuminate\Console\Command;

class FetchCurrencies extends Command
{
    protected $signature = 'fetch:currencies';
    protected $description = 'Fetching currencies for users accounts';

    protected $collector;

    public function __construct(CurrencyCollector $collector)
    {
        $this->collector = $collector;

        parent::__construct();
    }


    /**
     * Handler for fetching currencies of user which call every day
     */
    public function handle()
    {
        $currencies = UserRepository::getUsersCurrencies();

        $bar = $this->output->createProgressBar($currencies->count());

        foreach ($currencies as $currency)
        {
            try
            {
                $this->collector->create($currency);
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
