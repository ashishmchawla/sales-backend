<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\StatsController;

class CalculateStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:clearExistingCount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear count for existing stats monthly';

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
     * @return int
     */
    public function handle()
    {

       $stats = new StatsController();

       $storeStats = $stats->clearExistingCount();

    }
}