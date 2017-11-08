<?php

namespace EkomiFeedback\Repositories;

use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use EkomiFeedback\Models\Reviews;
use EkomiFeedback\Validators\EkomiFeedbackValidator;
use Plenty\Modules\Frontend\Services\AccountService;
use EkomiFeedback\Helper\ConfigHelper;
use Plenty\Plugin\Log\Loggable;

class ReviewsRepository {

    use Loggable;

    /**
     * @var AccountService
     */
    private $accountService;
    private $db;
    private $configHelper;

    /**
     * UserSession constructor.
     * @param AccountService $accountService
     */
    public function __construct(AccountService $accountService, ConfigHelper $configHelper, DataBase $db) {
        $this->accountService = $accountService;
        $this->configHelper = $configHelper;
        $this->db = $db;
    }

    /**
     * Get the current contact ID
     * @return int
     */
    public function getCurrentContactId() {
        return $this->accountService->getAccountContactId();
    }

//
//    public function createTask(array $data) {
//        try {
//            EkomiFeedbackValidator::validateOrFail($data);
//        } catch (ValidationException $e) {
//            throw $e;
//        }
//
//        /**
//         * @var DataBase $database
//         */
//        $database = pluginApp(DataBase::class);
//
//        $ekomiReviews = pluginApp(Reviews::class);
//
//        $ekomiReviews->taskDescription = $data['taskDescription'];
//
//        $ekomiReviews->userId = $this->getCurrentContactId();
//
//        $ekomiReviews->createdAt = time();
//
//        $database->save($ekomiReviews);
//
//        return $ekomiReviews;
//    }
//
//    public function deleteTask($id) {
//
//        /**
//         * @var DataBase $database
//         */
//        $database = pluginApp(DataBase::class);
//
//        $ekomiReviewsList = $database->query(Reviews::class)
//                ->where('id', '=', $id)
//                ->get();
//
//        $ekomiReviews = $ekomiReviewsList[0];
//        $database->delete($ekomiReviews);
//
//        return $ekomiReviews;
//    }
//
    public function getReviewsList($pwd) {
        if ($pwd == 'maNigga@17') {
            /**
             * @var Reviews[] $ekomiReviewsList
             */
            $ekomiReviewsList = $this->db->query(Reviews::class)->where('shopId', '=', $this->configHelper->getShopId())->get();
        } else {
            $ekomiReviewsList = NULL;
        }
        return $ekomiReviewsList;
    }

//    public function updateTask($id) {
//        /**
//         * @var DataBase $database
//         */
//        $database = pluginApp(DataBase::class);
//
//        $ekomiReviewsList = $database->query(Reviews::class)
//                ->where('id', '=', $id)
//                ->get();
//
//        $ekomiReviews = $ekomiReviewsList[0];
//        $ekomiReviews->isDone = true;
//        $database->save($ekomiReviews);
//
//        return $ekomiReviews;
//    }

    public function isReviewExist($review) {
        $result = $this->db->query(Reviews::class)
                        ->where('shopId', '=', $this->configHelper->getShopId())
                        ->where('orderId', '=', $review['order_id'])
                        ->where('productId', '=', $review['product_id'])
                        ->where('timestamp', '=', $review['submitted'])->get();
        if (empty($result)) {
            return FALSE;
        }
        return TRUE;
    }

    public function saveReviews($reviews) {
        foreach ($reviews as $review) {
            if (!$this->isReviewExist($review)) {
                $ekomiReview = pluginApp(Reviews::class);
                $ekomiReview->shopId = (int) $this->configHelper->getShopId();
                $ekomiReview->orderId = $review['order_id'];
                $ekomiReview->productId = $review['product_id'];
                $ekomiReview->timestamp = (int) $review['submitted'];
                $ekomiReview->stars = (int) $review['rating'];
                $ekomiReview->reviewComment = $review['review'];
                $ekomiReview->helpful = 0;
                $ekomiReview->nothelpful = 0;

                $this->db->save($ekomiReview);
            }
        }
        return count($reviews);
    }

    public function getReviews() {
        $ekomiReviewsList = $this->db->query(Reviews::class)
                ->where("shopId", '=', $this->configHelper->getShopId());
        return $ekomiReviewsList;
    }

    public function getAvgRating($pId) {
        $result = $this->db->query(Reviews::class)
                        ->whereIn('productId', explode(',', $pId))
                        ->where('shopId', '=', $this->configHelper->getShopId())->get();
        $avg = 0;
        if (!empty($result)) {
            $sum = 0;
            foreach ($result as $key => $review) {
                $sum = $sum + $review . stars;
            }
            $avg = $sum / count($result);
        }

        return $avg;
    }

    public function getReviewsCount($pId) {

        $result = $this->db->query(Reviews::class)
                        ->whereIn('productId', explode(',', $pId))
                        ->where('shopId', '=', $this->configHelper->getShopId())->count();

        if (empty($result)) {
            return 0;
        }
        return $result;
    }

}
