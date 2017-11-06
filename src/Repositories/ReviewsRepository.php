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
    private $ekomiReviews;

    /**
     * UserSession constructor.
     * @param AccountService $accountService
     */
    public function __construct(AccountService $accountService, ConfigHelper $configHelper, DataBase $db, Reviews $reviewsModel) {
        $this->accountService = $accountService;
        $this->configHelper = $configHelper;
        $this->db = $db;
        $this->ekomiReviews = $reviewsModel;
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
            $this->getLogger(__FUNCTION__)->error('EkomiFeedback::ReviewsRepository.isReviewExist', "not Exist" . json_encode($result));
            return FALSE;
        } else {
            $this->getLogger(__FUNCTION__)->error('EkomiFeedback::ReviewsRepository.isReviewExist', "Exist" . json_encode($review));
        }
        return TRUE;
    }

    public function saveReviews($reviews) {
        foreach ($reviews as $review) {
            if (!$this->isReviewExist($review)) {
                $this->ekomiReviews->shopId = (int) $this->configHelper->getShopId();
                $this->ekomiReviews->orderId = $review['order_id'];
                $this->ekomiReviews->productId = $review['product_id'];
                $this->ekomiReviews->timestamp = (int) $review['submitted'];
                $this->ekomiReviews->stars = (int) $review['rating'];
                $this->ekomiReviews->reviewComment = $review['review'];
                $this->ekomiReviews->helpful = 0;
                $this->ekomiReviews->nothelpful = 0;

                $this->db->save($this->ekomiReviews);
            }
        }
        return count($reviews);
    }

    public function getReviews() {
        $ekomiReviewsList = $this->db->query(Reviews::class)
                ->where("shopId", '=', $this->configHelper->getShopId());
        return $ekomiReviewsList;
    }

}
