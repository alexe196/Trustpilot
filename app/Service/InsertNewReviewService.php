<?php

namespace App\Service;

use App\Jobs\DownloadAvatarJob;
use App\Models\Review;

class InsertNewReviewService
{

    protected array $realLink = [];

    protected array $newReviews = [];

    public function __construct(array $newReviews)
    {
        $this->newReviews = $newReviews;
        $this->setRealLink();
    }

    protected function setRealLink()
    {
        foreach ($this->newReviews as $id => $item) {
            if(!empty($item['images']) && !empty($item['real_link'])) {
                $this->realLink[$id]['images'] = $item['images'];
                $this->realLink[$id]['real_link'] = $item['real_link'];
            }
            unset($this->newReviews[$id]['real_link']);
        }
    }

    public function insertNewReviews(): void
    {
        $existing = collect();
        $newReviews = collect($this->newReviews);

        Review::chunk(1000, function ($reviews) use (&$existing) {
            foreach ($reviews as $review) {
                $key = strtolower(trim($review->user_name)) . '|' . strtolower(trim($review->title));
                $existing->put($key, true);
            }
        });

        $unique = $newReviews->filter(function ($review) use ($existing) {
            $key = strtolower(trim($review['user_name'])) . '|' . strtolower(trim($review['title']));
            return !$existing->has($key);
        });

        $chunks = $unique->chunk(1000);

        foreach ($chunks as $chunk) {
            Review::insert($chunk->toArray());
        }

        $this->insertImageForReviewJob();
    }

    public function insertImageForReviewJob(): void
    {
        foreach ($this->realLink as $item) {
            DownloadAvatarJob::dispatch($item['real_link'], $item['images']);
        }
    }
}
