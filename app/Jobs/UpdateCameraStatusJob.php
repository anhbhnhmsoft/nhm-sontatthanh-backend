<?php

namespace App\Jobs;

use App\Models\Camera;
use App\Service\VideoLiveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Core\LogHelper;

class UpdateCameraStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Camera $camera;

    /**
     * Create a new job instance.
     */
    public function __construct(Camera $camera)
    {
        $this->camera = $camera;
    }

    /**
     * Execute the job.
     */
    public function handle(VideoLiveService $videoLiveService): void
    {
        try {
            LogHelper::debug("Bắt đầu cập nhật trạng thái camera ID: {$this->camera->id}");

            $result = $videoLiveService->updateCameraStatus($this->camera);

            if ($result) {
                LogHelper::debug("Cập nhật trạng thái camera ID: {$this->camera->id} thành công");
            } else {
                LogHelper::error("Cập nhật trạng thái camera ID: {$this->camera->id} thất bại");
            }
        } catch (\Throwable $th) {
            LogHelper::error("Lỗi khi cập nhật trạng thái camera ID: {$this->camera->id} - " . $th->getMessage());
            throw $th;
        }
    }
}
