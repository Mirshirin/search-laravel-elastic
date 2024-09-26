<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class CheckQueueStatusCommand extends Command
{
    protected $signature = 'queue:status';
    protected $description = 'Check queue status';

    public function handle()
    {
        $this->info('Checking queue status...');
        
        $pendingJobs = Queue::pendingCount();
        $failedJobs = Queue::failedCount();
        $completedJobs = Queue::completedCount();
        
        $this->info("Pending jobs: {$pendingJobs}");
        $this->info("Failed jobs: {$failedJobs}");
        $this->info("Completed jobs: {$completedJobs}");
        
        $this->info('Queue status check completed.');
    }
}
