<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\ReindexProductsJob;
use App\Services\ElasticsearchService;

class ManageQueueCommand extends Command
{
    protected $signature = 'queue:manage';
    protected $description = 'Manage queue';

    public function handle()
    {
        $this->info('Starting queue management...');

        // Add job to queue
        $job = new ReindexProductsJob(new ElasticsearchService());
        $job->dispatch()->delay(Carbon::now()->addHours(1));

        $this->info('Job added to queue successfully.');
    }
}
