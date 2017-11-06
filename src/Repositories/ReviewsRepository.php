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
//        $ekomiFeedbackReviews = pluginApp(Reviews::class);
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
//        $ekomiFeedbackReviewsList = $database->query(Reviews::class)
//                ->where('id', '=', $id)
//                ->get();
//
//        $ekomiFeedbackReviews = $ekomiFeedbackReviewsList[0];
//        $database->delete($ekomiFeedbackReviews);
//
//        return $ekomiFeedbackReviews;
//    }
//
    public function getReviewsList() {
        $database = pluginApp(DataBase::class);

        $id = $this->getCurrentContactId();
        /**
         * @var Reviews[] $ekomiFeedbackReviewsList
         */
        $ekomiFeedbackReviewsList = $database->query(Reviews::class)->where('id', '=', 1)->get();
        
         $this->getLogger(__FUNCTION__)->error('EkomiFeedback::ReviewsRepository.saveReviews', json_encode($ekomiFeedbackReviewsList));
         
        return $ekomiFeedbackReviewsList;
    }

//    public function updateTask($id) {
//        /**
//         * @var DataBase $database
//         */
//        $database = pluginApp(DataBase::class);
//
//        $ekomiFeedbackReviewsList = $database->query(Reviews::class)
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
        $result = $this->db->query(Reviews::class)
                        ->where('shopId', '=', $this->configHelper->getShopId())
                        ->where('orderId', '=', $review['order_id'])
                        ->where('productId', '=', $review['product_id'])
                        ->where('timestamp', '=', $review['submitted'])->get();
        return $result;
    }

    public function saveReviews($reviews) {

        $this->getLogger(__FUNCTION__)->error('EkomiFeedback::ReviewsRepository.saveReviews', json_encode($this->getReviews()));
        //[{"id":"1","shopId":0,"orderId":"","productId":"","timestamp":0,"stars":0,"reviewComment":"","helpful":0,"nothelpful":0}]
        foreach ($reviews as $review) {
            // if (!$this->isReviewExist($review)) {
            $database = pluginApp(DataBase::class);
            $ekomiFeedbackReviews = pluginApp(Reviews::class);
            $ekomiFeedbackReviews->shopId = (int) $this->configHelper->getShopId();
            $ekomiFeedbackReviews->orderId = $review['order_id'];
            $ekomiFeedbackReviews->productId = $review['product_id'];
            $ekomiFeedbackReviews->timestamp = (int) $review['submitted'];
            $ekomiFeedbackReviews->stars = (int) $review['rating'];
            $ekomiFeedbackReviews->reviewComment = $review['review'];
            $ekomiFeedbackReviews->helpful = 0;
            $ekomiFeedbackReviews->nothelpful = 0;

            $database->save($ekomiFeedbackReviews);
            // }
        }

        return $this->getReviews();
    }

    public function getReviews() {
        $ekomiFeedbackReviewsList = $this->db->query(Reviews::class)
                        ->where("shopId", '=', $this->configHelper->getShopId());
        return $ekomiFeedbackReviewsList;
    }

}
