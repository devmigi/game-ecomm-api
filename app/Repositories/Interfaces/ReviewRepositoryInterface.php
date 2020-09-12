<?php

namespace App\Repositories\Interfaces;

interface ReviewRepositoryInterface
{
    public function getProductReviews($productId);

    public function getReviewsByUser($userId);
}