<?php

namespace App\Console\Commands;

use App\Models\Camera;
use App\Service\VideoLiveService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FreshStateCamera extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fresh-state-camera';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh state of all enabled cameras from Imou API';

    /**
     * Execute the console command.
     */
    public function handle(VideoLiveService $videoLiveService)
    {
        $this->info('Starting camera state refresh...');

        // Get all enabled cameras that are not trashed
        $cameras = Camera::where('enable', true)->get();
        $total = $cameras->count();

        $this->info("Found {$total} enabled cameras.");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($cameras as $camera) {
            try {
                // Skip if not bound (optional, but logical)
                if (!$camera->bind_status || !$camera->device_id) {
                    $bar->advance();
                    continue;
                }

                // Sync channel info, which updates status/online state
                $result = $videoLiveService->viewLive($camera->device_id);

                if ($result->isError()) {
                    Log::error("Failed to refresh camera {$camera->device_id}: " . $result->getMessage());
                    // Optionally log to console output too
                    // $this->error("Failed: {$camera->device_id}");
                }
            } catch (\Exception $e) {
                Log::error("Exception refreshing camera {$camera->device_id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Camera state refresh completed.');
    }
}
