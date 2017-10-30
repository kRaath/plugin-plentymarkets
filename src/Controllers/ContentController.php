<?php

namespace EkomiFeedback\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Templates\Twig;
use EkomiFeedback\Services\EkomiServices;

use Plenty\Plugin\Http\Request;
use EkomiFeedback\Contracts\EkomiFeedbackReviewsRepositoryContract;

/**
 * Class ContentController
 * @package EkomiFeedback\Controllers
 */
class ContentController extends Controller {

    /**
     * @param Twig $twig
     * @return string
     */
    public function sendOrdersToEkomi(Twig $twig): string {

        /** @var \Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract $service */
        $service = pluginApp(EkomiServices::class);

        /** @var \Plenty\Modules\Authorization\Services\AuthHelper $authHelper */
        $authHelper = pluginApp(AuthHelper::class);

        $address = null;

//guarded
        $address = $authHelper->processUnguarded(
                function () use ($service, $address) {
            //unguarded
            return $service->sendOrdersData(7);
        }
        );
      //  $service->sendOrdersData(7);

        return $twig->render('EkomiFeedback::content.hello');
    }
    
    /**
     * @param Twig                   $twig
     * @param EkomiFeedbackReviewsRepositoryContract $ekomiFeedbackReviewsRepo
     * @return string
     */
    public function showReview(Twig $twig, EkomiFeedbackReviewsRepositoryContract $ekomiFeedbackReviewsRepo): string
    {
        $list = $ekomiFeedbackReviewsRepo->getReviewsList();
        $templateData = array("tasks" => $list);
        return $twig->render('EkomiFeedback::content.todo', $templateData);
    }
 
    /**
     * @param  \Plenty\Plugin\Http\Request $request
     * @param EkomiFeedbackReviewsRepositoryContract       $ekomiFeedbackReviewsRepo
     * @return string
     */
    public function createReview(Request $request, EkomiFeedbackReviewsRepositoryContract $ekomiFeedbackReviewsRepo): string
    {
        $newReview = $ekomiFeedbackReviewsRepo->createTask($request->all());
        return json_encode($newReview);
    }
 
    /**
     * @param int                    $id
     * @param EkomiFeedbackReviewsRepositoryContract $ekomiFeedbackReviewsRepo
     * @return string
     */
    public function updateReview(int $id, EkomiFeedbackReviewsRepositoryContract $ekomiFeedbackReviewsRepo): string
    {
        $updateReview = $ekomiFeedbackReviewsRepo->updateTask($id);
        return json_encode($updateReview);
    }
 
    /**
     * @param int                    $id
     * @param EkomiFeedbackReviewsRepositoryContract $ekomiFeedbackReviewsRepo
     * @return string
     */
    public function deleteReview(int $id, EkomiFeedbackReviewsRepositoryContract $ekomiFeedbackReviewsRepo): string
    {
        $deleteReview = $ekomiFeedbackReviewsRepo->deleteTask($id);
        return json_encode($deleteReview);
    }

}
