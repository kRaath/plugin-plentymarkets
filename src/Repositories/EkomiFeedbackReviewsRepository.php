<?php

namespace EkomiFeedback\Repositories;

use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use EkomiFeedback\Models\EkomiFeedbackReviews;
use EkomiFeedback\Validators\EkomiFeedbackValidator;
use Plenty\Modules\Frontend\Services\AccountService;
use EkomiFeedback\Helper\ConfigHelper;
use Plenty\Plugin\Log\Loggable;

class EkomiFeedbackReviewsRepository {

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
    public function __construct(AccountService $accountService, ConfigHelper $configHelper, DataBase $db, EkomiFeedbackReviews $reviewsModel) {
        $this->accountService = $accountService;
        $this->configHelper = $configHelper;
        $this->db = $db;
        $this->ekomiReviews = $reviewsModel;
    }

    /**
     * Get the current contact ID
     * @return int
     */
//    public function getCurrentContactId() {
//        return $this->accountService->getAccountContactId();
//    }
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
//        $ekomiFeedbackReviews = pluginApp(EkomiFeedbackReviews::class);
//
//        $ekomiFeedbackReviews->taskDescription = $data['taskDescription'];
//
//        $ekomiFeedbackReviews->userId = $this->getCurrentContactId();
//
//        $ekomiFeedbackReviews->createdAt = time();
//
//        $database->save($ekomiFeedbackReviews);
//
//        return $ekomiFeedbackReviews;
//    }
//
//    public function deleteTask($id) {
//
//        /**
//         * @var DataBase $database
//         */
//        $database = pluginApp(DataBase::class);
//
//        $ekomiFeedbackReviewsList = $database->query(EkomiFeedbackReviews::class)
//                ->where('id', '=', $id)
//                ->get();
//
//        $ekomiFeedbackReviews = $ekomiFeedbackReviewsList[0];
//        $database->delete($ekomiFeedbackReviews);
//
//        return $ekomiFeedbackReviews;
//    }
//
//    public function getReviewsList() {
//        $database = pluginApp(DataBase::class);
//
//        $id = $this->getCurrentContactId();
//        /**
//         * @var EkomiFeedbackReviews[] $ekomiFeedbackReviewsList
//         */
//        $ekomiFeedbackReviewsList = $database->query(EkomiFeedbackReviews::class)->where('userId', '=', $id)->get();
//        return $ekomiFeedbackReviewsList;
//    }

//    public function updateTask($id) {
//        /**
//         * @var DataBase $database
//         */
//        $database = pluginApp(DataBase::class);
//
//        $ekomiFeedbackReviewsList = $database->query(EkomiFeedbackReviews::class)
//                ->where('id', '=', $id)
//                ->get();
//
//        $ekomiFeedbackReviews = $ekomiFeedbackReviewsList[0];
//        $ekomiFeedbackReviews->isDone = true;
//        $database->save($ekomiFeedbackReviews);
//
//        return $ekomiFeedbackReviews;
//    }

    public function isReviewExist($review) {
        $result = $this->db->query(EkomiFeedbackReviews::class)
                        ->where('shopId', '=', $this->configHelper->getShopId())
                        ->where('orderId', '=', $review['order_id'])
                        ->where('productId', '=', $review['product_id'])
                        ->where('timestamp', '=', $review['submitted'])->get();
        return $result;
    }

    public function saveReviews($reviews) {

        foreach ($reviews as $review) {
            // if (!$this->isReviewExist($review)) {
            $this->ekomiReviews->shopId = (int)$this->configHelper->getShopId();
            $this->ekomiReviews->orderId = $review['order_id'];
            $this->ekomiReviews->productId = $review['product_id'];
            $this->ekomiReviews->timestamp = (int)$review['submitted'];
            $this->ekomiReviews->stars = (int)$review['rating'];
            $this->ekomiReviews->reviewComment = $review['review'];
            $this->ekomiReviews->helpful = 0;
            $this->ekomiReviews->nothelpful = 0;

            $this->db->save($this->ekomiReviews);
            // }
        }
        return $this->getReviews();
    }

    public function getReviews() {
        $ekomiFeedbackReviewsList = $this->db->query(EkomiFeedbackReviews::class)->get();
        return $ekomiFeedbackReviewsList;
    }

}
