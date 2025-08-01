<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'images',
        'user_name',
        'user_reviews_count',
        'rating',
        'title',
        'content',
        'review_date',
        'experience_date',
        'country',
    ];
}
