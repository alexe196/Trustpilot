<?php

namespace App\Traits;

use App\Models\Review;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use SplTempFileObject;

trait ExportToCsv
{

    CONST STORE_SEPARATOR = '/';
    protected $catalog = 'csv';
    protected $nameFile = 'reviews.csv';

    public function setNameFile($nameFile)
    {
        $this->nameFile = $nameFile;
    }

    public function getNameFile() {
        return $this->nameFile;
    }
    public function exportToCsvFile(): string
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        $csv->setDelimiter(';');
        $csv->setOutputBOM(Writer::BOM_UTF8);

        // Заголовки
        $csv->insertOne([
            'images',
            'user_name',
            'user_reviews_count',
            'rating',
            'title',
            'content',
            'review_date',
            'experience_date',
            'country'
        ]);

        try {
            Review::chunk(500, function ($reviews) use ($csv) {
                foreach ($reviews as $review) {
                    $csv->insertOne([
                        (string) $review->images,
                        (string) $review->user_name,
                        (int) $review->user_reviews_count,
                        (int) $review->rating,
                        (string) $review->title,
                        (string) $review->content,
                        (string) $review->review_date,
                        (string) $review->experience_date,
                        (string) $review->country,
                    ]);
                }
            });

            $path = $this->catalog.static::STORE_SEPARATOR.$this->nameFile;

            // Сохраняем CSV
            Storage::disk('public')->put($path, $csv->getContent());

            return $path;

        } catch (\Exception $e) {
            Log::error('Ошибка при создании CSV: ' . $e->getMessage());
            return '';
        }
    }
}
