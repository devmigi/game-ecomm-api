<?php

namespace App\Repositories;

use App\Models\Review;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class ReviewRepository implements ReviewRepositoryInterface
{
    public function getProductReviews($producId){

        $reviews = Cache::rememberForever("products.{$producId}.reviews", function () use ($producId) {
            return Review::with(['product.images.image', 'user'])->where('product_id', $producId)->where('verified', true)->where('deleted', false)->get();
        });

        return $reviews;
    }


    public function getReviewsByUser($userId){
        $reviews = Cache::rememberForever("users.{$userId}.reviews", function () use ($userId) {
            return Review::with(['product.images.image', 'user'])->where('user_id', $userId)->where('deleted', false)->get();
        });

        return $reviews;
    }

}