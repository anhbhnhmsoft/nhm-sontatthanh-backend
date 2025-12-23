<?php

namespace App\Console\Commands;

use App\Service\VideoLiveService;
use Illuminate\Console\Command;

class RefreshAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh-access-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Làm mới access token để  truy cập vào Imou';

    /**
     * Execute the console command.
     */
    public function handle(VideoLiveService $videoLiveService)
    {
        $videoLiveService->setAccessToken();
    }
}
