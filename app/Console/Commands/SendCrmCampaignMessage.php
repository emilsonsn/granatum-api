<?php

namespace App\Console\Commands;

use App\Services\Routine\RoutineService;
use Illuminate\Console\Command;

class SendCrmCampaignMessage extends Command
{
    public $routineService;

    public function __construct(RoutineService $routineService) {
        parent::__construct();
        $this->routineService = $routineService;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-crm-campaign-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->routineService->sendCrmMessage();
    }
}
