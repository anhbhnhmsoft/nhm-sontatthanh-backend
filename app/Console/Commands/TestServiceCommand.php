<?php

namespace App\Console\Commands;

use App\Service\VideoLiveService;
use Illuminate\Console\Command;

class TestServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-service-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(VideoLiveService $videoLiveService)
    {
        $videoLiveService->bindDevice('D29D3BCPCG6A1AF', 'L2EB5FB8');
    }
}
