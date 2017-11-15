<?php

namespace EkomiFeedback\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Templates\Twig;
use Plenty\Modules\Authorization\Services\AuthHelper;
use EkomiFeedback\Services\EkomiServices;
use Plenty\Plugin\Http\Request;
use EkomiFeedback\Repositories\ReviewsRepository;
use Plenty\Plugin\Log\Loggable;

/**
 * Class ContentController
 * @package EkomiFeedback\Controllers
 */
class ContentController extends Controller {

    use Loggable;

    /**
     * @param Twig $twig
     * @return string
     */
    public function sendOrdersToEkomi(Twig $twig): string {

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

    /**
     * Fetches all product reviews by calling getProductFeedback api
     * 
     * @param Twig $twig
     * @param ReviewsRepository $reviewsRepo
     * @return string
     */
    public function fetchProductReviews(Twig $twig, ReviewsRepository $reviewsRepo): string {
        $service = pluginApp(EkomiServices::class);

        $reviews = $service->fetchProductReviews($range = 'all');

        $templateData = array("reviewsCount" => $reviews);

        return $twig->render('EkomiFeedback::content.reviewsSuccess', $templateData);
    }

    /**
     * Shows all reviews
     * 
     * @param Twig                   $twig
     * @param ReviewsRepository $reviewsRepo
     * @return string
     */
    public function showReview(string $pwd, Twig $twig, ReviewsRepository $reviewsRepo): string {
        $list = $reviewsRepo->getReviewsList($pwd);
        $templateData = array("tasks" => $list);
        return $twig->render('EkomiFeedback::content.reviews', $templateData);
    }

    /**
     * Loads Reviews by ajax call
     * 
     * @param  \Plenty\Plugin\Http\Request $request
     * @param ReviewsRepository       $reviewsRepo
     * @return string
     */
    public function loadReviews(Request $request, ReviewsRepository $reviewsRepo, Twig $twig): string {
        $data = $request->all();
        if (!empty($data)) {
            $itemID = trim($data['prcItemID']);
            $offset = (int) trim($data['prcOffset']);
            $limit = (int) trim($data['reviewsLimit']);
            $filter_type = trim($data['prcFilter']);

            $reviews = $reviewsRepo->getReviews($itemID, $offset, $limit, $filter_type);

            $result = $twig->render('EkomiFeedback::content.reviewsContainerPartial', ['reviews' => $reviews]);

            return json_encode(['result' => $result, 'count' => count($reviews), 'state' => 'success', 'message' => 'reviews fetched']);
        } else {
            $this->getLogger(__FUNCTION__)->error('EkomiFeedback::ContentController.loadReviews', ['state' => 'error', 'message' => 'empty data fields', 'data' => $data]);

            return json_encode(['state' => 'error', 'message' => 'empty data fields', 'data' => $data]);
        }
    }

    /**
     * Saves Feeback
     * 
     * @param  \Plenty\Plugin\Http\Request $request
     * @param ReviewsRepository       $reviewsRepo
     * @return string
     */
    public function saveFeedback(Request $request, ReviewsRepository $reviewsRepo): string {
        $data = $request->all();

        $response = array(
            'state' => '',
            'message' => ''
        );

        if (!empty($data)) {
            $itemID = trim($data['prcItemID']);
            $reviewId = trim($data['review_id']);
            $helpfulness = trim($data['helpfulness']);

            $review = $reviewsRepo->rateReview($itemID, (int) $reviewId, $helpfulness);

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

            $this->getLogger(__FUNCTION__)->error('EkomiFeedback::ContentController.saveFeedback', $response);
        }

        return json_encode($response);
    }

}
