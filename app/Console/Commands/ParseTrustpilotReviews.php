<?php

namespace App\Console\Commands;

use App\Service\InsertNewReviewService;
use App\Traits\ParseTrustpilo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class ParseTrustpilotReviews extends Command
{

    use ParseTrustpilo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:parse-trustpilot-reviews';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command parser';

    protected $client;

    protected $dataParse = [];

    protected int $item = 0;

    public function __construct()
    {
        parent::__construct();
        $this->client = HttpClient::create();
    }

    public function handle()
    {
        $this->item = 0;
        $path = storage_path('app/public/parce-uploads/links.txt');

        if (!file_exists($path)) {
            return back()->withErrors(['Файл links.txt не найден в storage/app/public/parce-uploads.']);
        }

        try {
            $urls = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        } catch (\Throwable $e) {
            Log::error('Ошибка при загрузке ссылок: ' . $e->getMessage());
            $this->error('Ошибка: ' . $e->getMessage());
        }

        foreach ($urls as $url) {
            $page = 1;
            do {
                $fullUrl = $url . '?page=' . $page;
                $this->info("Парсинг: $fullUrl");
                $html = $this->fetchHtml($fullUrl);
                if (!$html) {
                    break;
                }

                $crawler = new Crawler($html);
                $reviewNodes = $crawler->filter('.CDS_Card_card__16d1cc.styles_reviewCard__Qwhpy');

                if ($reviewNodes->count() === 0) {
                    $this->info("Отзывы не найдены на: $url");
                    continue;
                }

                $reviewNodes->each(function (Crawler $reviewNode) use ($url)
                {
                    try {
                        $this->item++;
                        $avatarUrl = $reviewNode->filter('.CDS_Avatar_imageWrapper__aedbfa img')->count()
                            ? $reviewNode->filter('.CDS_Avatar_imageWrapper__aedbfa img')->attr('src')
                            : '';

                        $this->dataParse[$this->item]['images'] = $this->downloadAvatar($avatarUrl);
                        $this->dataParse[$this->item]['real_link'] = $avatarUrl;

                        if ($reviewNode->filter('span.CDS_Typography_appearance-default__96c1da.CDS_Typography_prettyStyle__96c1da.CDS_Typography_heading-xs__96c1da.styles_consumerName__xKr9c')->count()) {
                            $this->dataParse[$this->item]['user_name'] = $reviewNode->filter('span.CDS_Typography_appearance-default__96c1da.CDS_Typography_prettyStyle__96c1da.CDS_Typography_heading-xs__96c1da.styles_consumerName__xKr9c')->text();
                        } else {
                            $this->dataParse[$this->item]['user_name'] = '';
                        }

                        if ($reviewNode->filter('span[data-consumer-reviews-count-typography="true"]')->count()) {
                            $text = $reviewNode->filter('span[data-consumer-reviews-count-typography="true"]')->text();
                            preg_match('/\d+/', $text, $matches);
                            $this->dataParse[$this->item]['user_reviews_count'] = $matches[0] ?? null;
                        } else {
                            $this->dataParse[$this->item]['user_reviews_count'] = '';
                        }

                        if ($reviewNode->filter('.CDS_StarRating_starRating__8ae3cf')->count()) {
                            $ratingText = $reviewNode->filter('img.CDS_StarRating_starRating__8ae3cf')->attr('alt');
                            preg_match('/Rated (\d) out of 5 stars/', $ratingText, $matches);
                            $this->dataParse[$this->item]['rating'] = isset($matches[1]) ? (int)$matches[1] : null;
                        } else {
                            $this->dataParse[$this->item]['rating'] = '';
                        }

                        if ($reviewNode->filter('h2[data-service-review-title-typography]')->count()) {
                            $this->dataParse[$this->item]['title'] = $reviewNode->filter('h2[data-service-review-title-typography]')->text();
                        } else {
                            $this->dataParse[$this->item]['title'] = '';
                        }

                        if ($reviewNode->filter('p[data-service-review-text-typography]')->count()) {
                            $this->dataParse[$this->item]['content'] = $reviewNode->filter('p[data-service-review-text-typography]')->text();
                        } else {
                            $this->dataParse[$this->item]['content'] = '';
                        }

                        if ($reviewNode->filter('time[data-service-review-date-time-ago]')->count()) {
                            $this->dataParse[$this->item]['review_date'] = $reviewNode->filter('time[data-service-review-date-time-ago]')->text();
                        } else {
                            $this->dataParse[$this->item]['review_date'] = '';
                        }

                        if ($reviewNode->filter('.CDS_Typography_appearance-subtle__96c1da CDS_Typography_prettyStyle__96c1da CDS_Typography_body-m__96c1da')->count()) {
                            $this->dataParse[$this->item]['experience_date'] = $reviewNode->filter('.CDS_Typography_appearance-subtle__96c1da CDS_Typography_prettyStyle__96c1da CDS_Typography_body-m__96c1da')->text();
                        } else {
                            $this->dataParse[$this->item]['experience_date'] = '';
                        }


                        $this->dataParse[$this->item]['country'] = 'United Kingdom';

                    } catch (\Exception $e) {
                        Log::error('Ошибка парсинга отзыва: ' . $e->getMessage());
                    }
                });

                $page++;
            } while (true);

            $this->info('Готово!');
        }

        $this->info('Все загруженно в базу!');

        (new InsertNewReviewService($this->dataParse))->insertNewReviews();
    }

    protected function fetchHtml(string $url): ?string
    {
        try {
            $response = $this->client->request('GET', $url);
            return $response->getContent();
        } catch (\Exception $e) {
            Log::error('Ошибка при загрузке страницы: ' . $url . ' — ' . $e->getMessage());
            return null;
        }
    }
}
