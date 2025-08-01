<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DownloadAvatarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $url;
    protected $path = 'image';
    protected string $avatarPath;

    public function __construct(string $url, string $avatarPath)
    {
        $this->url = $url;
        $this->avatarPath = $avatarPath;
    }

    public function handle(): void
    {
        if(!empty($this->avatarPath) && !empty($this->url)) {
            if (Storage::disk('public')->exists($this->path)) {
                try {
                    $contents = file_get_contents($this->url);
                    if ($contents !== false) {
                        Storage::disk('public')->put($this->avatarPath, $contents);
                    }
                } catch (\Throwable $e) {
                    Log::error('Failed to download avatar: ' . $e->getMessage());
                }
            }
        }
    }
}
