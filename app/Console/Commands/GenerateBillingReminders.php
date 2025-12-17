<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class GenerateBillingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:generate-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate reminders for unpaid and overdue billings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = ReminderService::generateReminders();
        $this->info("Generated {$count} new billing reminders.");
    }
}
