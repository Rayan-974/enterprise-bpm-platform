<?php

namespace App\Console\Commands;

use App\Services\SLAEngineService;
use Illuminate\Console\Command;

class CheckSlaBreachesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bpm:check-sla';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan pending tasks for SLA breaches and auto-escalate overdue steps';

    /**
     * Execute the console command.
     */
    public function handle(SLAEngineService $slaEngine): int
    {
        $this->info('Scanning workflow tasks for SLA breaches...');

        $result = $slaEngine->checkAndEscalateOverdueTasks();

        $this->info("SLA check completed. Checked: {$result['checked']}, Escalated: {$result['escalated']}");

        return Command::SUCCESS;
    }
}
