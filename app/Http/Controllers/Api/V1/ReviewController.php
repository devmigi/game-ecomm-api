<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Repositories\Interfaces\ReviewRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ReviewController extends ApiController
{
    private $reviewRepository;

    public function __construct(ReviewRepositoryInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }


    /**
     * API - Add new Review
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|max:5|min:0'
        ]);

        $user = $request->user();

        if ($validator->fails()) {
            return $this->sendError('Validation failed.',  $validator->errors());
        }

        $input = $request->all();

        $review = new Review();
        $review->user_id = $user->id;
        $review->product_id = $input['product_id'];
        $review->rating = $input['rating'];
        $review->title = $input['title'];
        $review->comment = $input['comment'];
        $saved = $review->save();

        // clear cache
        Cache::forget("users.{$user->id}.reviews");
        Cache::forget("products.{$input['product_id']}.reviews");

        return $this->sendResponse(['added' => $saved], 'Successfully added.');

    }

    /**
     * API - Update a Review
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function update(Request $request, Review $review)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|max:5|min:0'
        ]);

        $user = $request->user();

        if ($validator->fails()) {
            return $this->sendError('Validation failed.',  $validator->errors());
        }

        $input = $request->all();

        $review->rating = $input['rating'];
        $review->title = $input['title'];
        $review->comment = $input['comment'];
        $saved = $review->save();

        // clear cache
        Cache::forget("users.{$user->id}.reviews");
        Cache::forget("products.{$review->product_id}.reviews");

        return $this->sendResponse(['added' => $saved], 'Successfully updated.');

    }



    /**
     * Get Product Reviews.
     *
     * @return \Illuminate\Http\Response
     */
    public function productReviews($productId)
    {
        $reviews = $this->reviewRepository->getProductReviews($productId);

        return $this->sendResponse(['reviews' => ReviewResource::collection($reviews)], 'product reviews');
    }



    /**
     * API - Get user's Wishlist
     *
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     */
    public function userReviews(Request $request)
    {

        $reviews = $this->reviewRepository->getReviewsByUser($request->user()->id);

        return $this->sendResponse(['reviews' => ReviewResource::collection($reviews)], 'user reviews');
    }

}
