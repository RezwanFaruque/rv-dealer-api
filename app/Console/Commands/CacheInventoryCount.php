<?php

namespace App\Console\Commands;

use App\Syncing\Caching;
use App\Syncing\CachingResourceInventoryCount;
use App\Syncing\Syncer;
use Illuminate\Console\Command;

class CacheInventoryCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:counts {--b|brands : Cache inventory count only brands} {--m|models : Cache inventory count only models} {--t|types : Cache inventory count only types}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache inventory count for brands & models & types';

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

        $only_brands = ($this->option('brands')) ? $this->option('brands') : false;
        $only_models = ($this->option('models')) ? $this->option('models') : false;
        $only_types = ($this->option('types')) ? $this->option('types') : false;

        $cacheClient = new CachingResourceInventoryCount();
        $cacheClient->enableConsole($this);

        if( $only_brands ||  $only_models || $only_types ){
            ($only_brands) ? $cacheClient->brands() : null;
            ($only_models) ? $cacheClient->models() : null;
            ($only_types) ? $cacheClient->types() : null;

        }
        else
        {
            // cache both...
            $cacheClient->brands();
            $cacheClient->models();
            $cacheClient->types();
        }
    }
}
