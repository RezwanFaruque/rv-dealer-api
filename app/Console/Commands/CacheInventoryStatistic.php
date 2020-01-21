<?php

namespace App\Console\Commands;

use App\Statistic;
use App\Syncing\Caching;
use App\Syncing\CachingResourceInventoryCount;
use App\Syncing\Syncer;
use Illuminate\Console\Command;

class CacheInventoryStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache statistics about the inventory';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $timezone = "America/Chicago";
        $date_format = "n/j/Y g:i A";
        $current_date = new \DateTime('now', new \DateTimeZone($timezone) );
        $current_date = $current_date->format($date_format);

        $start_msg = "Last syncing has started at $current_date ...";
        $end_msg = "Job done.";

        $this->info($start_msg );
        Statistic::sync();
        $this->info($end_msg );

    }
}
