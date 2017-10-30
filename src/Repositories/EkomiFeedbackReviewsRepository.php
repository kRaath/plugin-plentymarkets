<?php

namespace EkomiFeedback\Repositories;

use Plenty\Exceptions\ValidationException;
use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use EkomiFeedback\Contracts\EkomiFeedbackReviewsRepositoryContract;
use EkomiFeedback\Models\EkomiFeedbackReviews;
use EkomiFeedback\Validators\EkomiFeedbackValidator;
use Plenty\Modules\Frontend\Services\AccountService;

class EkomiFeedbackReviewsRepository implements EkomiFeedbackReviewsRepositoryContract {

    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * UserSession constructor.
     * @param AccountService $accountService
     */
    public function __construct(AccountService $accountService) {
        $this->accountService = $accountService;
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

}
