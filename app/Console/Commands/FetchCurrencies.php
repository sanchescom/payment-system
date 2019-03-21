<?php

namespace App\Console\Commands;

use App\Repositories\UserRepository;
use App\Services\CurrencyCollector;
use Illuminate\Console\Command;

/**
 * This class console command for fetching user's currencies rates.
 *
 * Class FetchCurrencies.
 */
class FetchCurrencies extends Command
{
    /**
     * @var string
     */
    protected $signature = 'fetch:currencies';

    /**
     * @var string
     */
    protected $description = 'Fetching currencies for users accounts';

    /**
     * @var CurrencyCollector
     */
    protected $collector;

    public function __construct(CurrencyCollector $collector)
    {
        parent::__construct();

        $this->collector = $collector;
    }

    /**
     * Handler for fetching currencies of user which call every day.
     *
     * @return void
     */
    public function handle()
    {
        $currencies = UserRepository::getUsersCurrencies();

        $bar = $this->output->createProgressBar($currencies->count());

        foreach ($currencies as $currency) {
            try {
                $this->collector->create($currency);
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
            }
            $bar->advance();
        }
        $bar->finish();
    }
}
