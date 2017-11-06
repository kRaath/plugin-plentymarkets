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

        $service = pluginApp(EkomiServices::class);
        
        $service->fetchProductReviews($range = '1w');

        return $twig->render('EkomiFeedback::content.hello');
    }

    public function fetchProductReviews(Twig $twig, ReviewsRepository $ekomiReviewsRepo): string {
        $service = pluginApp(EkomiServices::class);

        $reviews = $service->fetchProductReviews($range = 'all');

        $templateData = array("reviewsCount" => count($reviews));

        return $twig->render('EkomiFeedback::content.reviewsSuccess', $templateData);
    }

    /**
     * @param Twig                   $twig
     * @param ReviewsRepository $ekomiReviewsRepo
     * @return string
     */
    public function showReview(string $pwd, Twig $twig, ReviewsRepository $ekomiReviewsRepo): string {
        $list = $ekomiReviewsRepo->getReviewsList($pwd);
        $templateData = array("tasks" => $list);
        return $twig->render('EkomiFeedback::content.reviews', $templateData);
    }

    /**
     * @param  \Plenty\Plugin\Http\Request $request
     * @param ReviewsRepository       $ekomiReviewsRepo
     * @return string
     */
//    public function createReview(Request $request, ReviewsRepository $ekomiReviewsRepo): string {
//        $newReview = $ekomiReviewsRepo->createTask($request->all());
//        return json_encode($newReview);
//    }

    /**
     * @param int                    $id
     * @param ReviewsRepository $ekomiReviewsRepo
     * @return string
     */
//    public function updateReview(int $id, ReviewsRepository $ekomiReviewsRepo): string {
//        $updateReview = $ekomiReviewsRepo->updateTask($id);
//        return json_encode($updateReview);
//    }

    /**
     * @param int                    $id
     * @param ReviewsRepository $ekomiReviewsRepo
     * @return string
     */
//    public function deleteReview(int $id, ReviewsRepository $ekomiReviewsRepo): string {
//        $deleteReview = $ekomiReviewsRepo->deleteTask($id);
//        return json_encode($deleteReview);
//    }
}
