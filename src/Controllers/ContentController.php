<?php

namespace EkomiFeedback\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Templates\Twig;
use Plenty\Modules\Authorization\Services\AuthHelper;
use EkomiFeedback\Services\EkomiServices;
use Plenty\Plugin\Http\Request;
use EkomiFeedback\Repositories\ReviewsRepository;

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

    public function fetchProductReviews(Twig $twig, ReviewsRepository $ekomiFeedbackReviewsRepo): string {

        /** @var \Plenty\Modules\Account\Address\Contracts\AddressRepositoryContract $service */
        $service = pluginApp(EkomiServices::class);

        $reviews = $service->fetchProductReviews($range = '1w');

        $templateData = array("reviews" => $reviews);

        return $twig->render('EkomiFeedback::content.reviews', $templateData);
    }

    /**
     * @param Twig                   $twig
     * @param ReviewsRepository $ekomiFeedbackReviewsRepo
     * @return string
     */
    public function showReview(string $pwd, Twig $twig, ReviewsRepository $ekomiFeedbackReviewsRepo): string {
        $list = $ekomiFeedbackReviewsRepo->getReviewsList($pwd);
        $templateData = array("tasks" => $list);
        return $twig->render('EkomiFeedback::content.review', $templateData);
    }

    /**
     * @param  \Plenty\Plugin\Http\Request $request
     * @param ReviewsRepository       $ekomiFeedbackReviewsRepo
     * @return string
     */
//    public function createReview(Request $request, ReviewsRepository $ekomiFeedbackReviewsRepo): string {
//        $newReview = $ekomiFeedbackReviewsRepo->createTask($request->all());
//        return json_encode($newReview);
//    }

    /**
     * @param int                    $id
     * @param ReviewsRepository $ekomiFeedbackReviewsRepo
     * @return string
     */
//    public function updateReview(int $id, ReviewsRepository $ekomiFeedbackReviewsRepo): string {
//        $updateReview = $ekomiFeedbackReviewsRepo->updateTask($id);
//        return json_encode($updateReview);
//    }

    /**
     * @param int                    $id
     * @param ReviewsRepository $ekomiFeedbackReviewsRepo
     * @return string
     */
//    public function deleteReview(int $id, ReviewsRepository $ekomiFeedbackReviewsRepo): string {
//        $deleteReview = $ekomiFeedbackReviewsRepo->deleteTask($id);
//        return json_encode($deleteReview);
//    }
}
