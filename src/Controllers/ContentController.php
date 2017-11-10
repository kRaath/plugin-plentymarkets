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

        $templateData = array("reviewsCount" => $reviews);

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
    public function loadReviews(Request $request, ReviewsRepository $ekomiReviewsRepo, Twig $twig): string {
        $data = $request->all();
        if (!empty($data)) {
            $itemID = trim($data['prcItemID']);
            $offset = trim($data['prcOffset']);
            $limit = trim($data['reviewsLimit']);
            $filter_type = trim($data['prcFilter']);

            $reviews = $ekomiReviewsRepo->getReviews($itemID, $offset, $limit, $filter_type);

            $result = $twig->render('EkomiFeedback::content.reviewsContainerPartial', ['reviews' => $reviews]);

            return json_encode(['result' => $result, 'count' => count($reviews), 'state' => 'success', 'message' => 'reviews fetched']);
        } else {
            return json_encode(['state' => 'error', 'message' => 'empty data fields', 'data' => $data]);
        }
    }

    /**
     * @param  \Plenty\Plugin\Http\Request $request
     * @param ReviewsRepository       $ekomiReviewsRepo
     * @return string
     */
    public function saveFeedback(Request $request, ReviewsRepository $ekomiReviewsRepo): string {
        $data = $request->all();

        $response = array(
            'state' => '',
            'message' => ''
        );

        if (!empty($data)) {
            $itemID = trim($data['prcItemID']);
            $reviewId = trim($data['review_id']);
            $helpfulness = trim($data['helpfulness']);

            $review = $ekomiReviewsRepo->rateReview($itemID, $reviewId, $helpfulness);

            if (!empty($review)) {
                $message = ($review->helpful) . ' out of ' . ($review->helpful + $review->nothelpful) . ' people found this review helpful';

                $response['state'] = 'success';
                $response['message'] = $message;
                $response['rateHelpfulness'] = $helpfulness == '1' ? 'helpful' : 'nothelpful';
            } else {
                $response['state'] = 'success';
                $response['message'] = "Something went wrong! Ma be review_id {$reviewId} not exist!";
                $response['data'] = $data;
            }
        } else {
            $response['state'] = 'success';
            $response['message'] = 'Missing data fields';
            $response['data'] = $data;
        }

        return json_encode($response);
    }

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
