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
    private $configHelper;
    /**
     * UserSession constructor.
     * @param AccountService $accountService
     */
    public function __construct(AccountService $accountService,ConfigHelper $configHelper) {
        $this->accountService = $accountService;
        $this->configHelper = $configHelper;
    }

    /**
     * Get the current contact ID
     * @return int
     */
    public function getCurrentContactId() {
        return $this->accountService->getAccountContactId();
    }

    public function createTask(array $data) {
        try {
            EkomiFeedbackValidator::validateOrFail($data);
        } catch (ValidationException $e) {
            throw $e;
        }

        /**
         * @var DataBase $database
         */
        $database = pluginApp(DataBase::class);

        $ekomiFeedbackReviews = pluginApp(EkomiFeedbackReviews::class);

        $ekomiFeedbackReviews->taskDescription = $data['taskDescription'];

        $ekomiFeedbackReviews->userId = $this->getCurrentContactId();

        $ekomiFeedbackReviews->createdAt = time();

        $database->save($ekomiFeedbackReviews);

        return $ekomiFeedbackReviews;
    }

    public function deleteTask($id) {

        /**
         * @var DataBase $database
         */
        $database = pluginApp(DataBase::class);

        $ekomiFeedbackReviewsList = $database->query(EkomiFeedbackReviews::class)
                ->where('id', '=', $id)
                ->get();

        $ekomiFeedbackReviews = $ekomiFeedbackReviewsList[0];
        $database->delete($ekomiFeedbackReviews);

        return $ekomiFeedbackReviews;
    }

    public function getReviewsList() {
        $database = pluginApp(DataBase::class);

        $id = $this->getCurrentContactId();
        /**
         * @var EkomiFeedbackReviews[] $ekomiFeedbackReviewsList
         */
        $ekomiFeedbackReviewsList = $database->query(EkomiFeedbackReviews::class)->where('userId', '=', $id)->get();
        return $ekomiFeedbackReviewsList;
    }

    public function updateTask($id) {
        /**
         * @var DataBase $database
         */
        $database = pluginApp(DataBase::class);

        $ekomiFeedbackReviewsList = $database->query(EkomiFeedbackReviews::class)
                ->where('id', '=', $id)
                ->get();

        $ekomiFeedbackReviews = $ekomiFeedbackReviewsList[0];
        $ekomiFeedbackReviews->isDone = true;
        $database->save($ekomiFeedbackReviews);

        return $ekomiFeedbackReviews;
    }

    public function saveReviews(array $reviews) {

        foreach ($reviews as $review) {
           // if (!$this->isReviewExist($config, $review)) {
                $insertData = array(
                    'shopId' => $this->configHelper->getShopId(),
                    'orderId' => $review['order_id'],
                    'productId' => $review['product_id'],
                    'timestamp' => $review['submitted'],
                    'stars' => $review['rating'],
                    'reviewComment' => $review['review'],
                    'helpful' => 0,
                    'nothelpful' => 0
                );
                
                //}
        }
        return TRUE;

        /**
         * @var DataBase $database
         */
        $database = pluginApp(DataBase::class);

        $ekomiFeedbackReviews = pluginApp(EkomiFeedbackReviews::class);

        $ekomiFeedbackReviews->taskDescription = 'yeyeye';

        $ekomiFeedbackReviews->userId = $this->getCurrentContactId();

        $ekomiFeedbackReviews->createdAt = time();

        $database->save($ekomiFeedbackReviews);

        return $ekomiFeedbackReviews;
    }

}
