<?php

namespace App\Traits;

use App\Jobs\DownloadAvatarJob;

trait ParseTrustpilo
{

    CONST STORE_SEPARATOR = '/';
    protected $userCatalog = 'image';


    public function downloadAvatar($avatarUrl): string
    {
        $avatarPath = '';
        if(!empty($avatarUrl)) {
            $avatarName = basename(parse_url($avatarUrl, PHP_URL_PATH));
            $avatarPath = $this->userCatalog . static::STORE_SEPARATOR . uniqid() . $avatarName;
        }

        return $avatarPath;
    }

    public function countReview($totalReviews): int
    {
        preg_match('/\d+/', $totalReviews, $matches);
        return $matches[0] ?? 0;
    }

}
