<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CameraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => Storage::disk('public')->url($this->image) ?? null,
            'is_active' => $this->is_active,
            'device_id' => $this->device_id,
            'channel_id' => $this->channel_id,
            'enable' => $this->enable,
            'channels' => $this->channels->map(function ($channel) {
                return [
                    'id' => (string) $channel->id,
                    'name' => $channel->name,
                    'status' => $channel->status,
                    'position' => $channel->position,
                    'is_activated' => $channel->is_activated,
                    'has_stream' => $channel->has_stream,
                    'live_token' => $channel->live_token,
                    'live_url_hls' => $channel->live_url_hls,
                    'live_url_https' => $channel->live_url_https,
                ];
            }),
        ];
    }
}
