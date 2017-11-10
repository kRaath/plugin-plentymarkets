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

    /**
     * UserSession constructor.
     * @param AccountService $accountService
     */
    public function __construct(AccountService $accountService, ConfigHelper $configHelper, DataBase $db) {
        $this->accountService = $accountService;
        $this->configHelper = $configHelper;
        $this->db = $db;
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
            return FALSE;
        }
        return TRUE;
    }

    public function saveReviews($reviews) {
        foreach ($reviews as $review) {
            if (!$this->isReviewExist($review)) {
                $ekomiReview = pluginApp(Reviews::class);
                $ekomiReview->shopId = (int) $this->configHelper->getShopId();
                $ekomiReview->orderId = $review['order_id'];
                $ekomiReview->productId = $review['product_id'];
                $ekomiReview->timestamp = (int) $review['submitted'];
                $ekomiReview->stars = (int) $review['rating'];
                $ekomiReview->reviewComment = $review['review'];
                $ekomiReview->helpful = 0;
                $ekomiReview->nothelpful = 0;

                $this->db->save($ekomiReview);
            }
        }
        return count($reviews);
    }

    public function getMiniStarsStats($item) {

        $data = array('count' => 0, 'avg' => 0, 'itemName' => '');

        $itemID = $this->getItemIDs($item);

        if ($itemID) {
            $result = $this->db->query(Reviews::class)
                            ->whereIn('productId', explode(',', $itemID))
                            ->where('shopId', '=', $this->configHelper->getShopId())->get();

            if (!empty($result)) {
                $data['count'] = count($result);
                $sum = 0;
                foreach ($result as $key => $review) {
                    $sum = $sum + $review->stars;
                }
                $data['avg'] = $sum / $data['count'];
            }
            $data['itemName'] = $this->getItemName($item);
        }

        return $data;
    }

    public function getReviewsCount($item) {
        $itemID = $this->getItemIDs($item);
        if ($itemID) {
            $result = $this->db->query(Reviews::class)
                            ->whereIn('productId', explode(',', $itemID))
                            ->where('shopId', '=', $this->configHelper->getShopId())->count();

            if (!empty($result)) {
                return $result;
            }
        }
        return 0;
    }

    /**
     * Counts the stars
     * 
     * @return array The star counts array
     */
    public function getReviewsContainerStats($item, $offset, $limit) {
        $this->getLogger(__FUNCTION__)->error('EkomiFeedback::ReviewsRepository.getReviewsContainerStats', $item);

        $itemID = $this->getItemIDs($item);
        if (!$itemID) {
            $this->getLogger(__FUNCTION__)->error('EkomiFeedback::ReviewsRepository.getReviewsContainerStats', 'ItemId is Null:' . $itemID);
            return NULL;
        }

        $result = $this->db->query(Reviews::class)
                        ->whereIn('productId', explode(',', $itemID))
                        ->where('shopId', '=', $this->configHelper->getShopId())->get();
        $avg = 0;
        $reviewsCountTotal = 0;
        $starsCountArray = array();

        if (!empty($result)) {
            $reviewsCountTotal = count($result);
            $sum = 0;
            foreach ($result as $key => $review) {
                $sum = $sum + $review->stars;

                if (!isset($starsCountArray[$review->stars])) {
                    $starsCountArray[$review->stars] = array('total' => 0, 'avg' => 0);
                }
                $starsCountArray[$review->stars]['total'] = 1 + $starsCountArray[$review->stars]['total'];
            }
            // set count for all stars
            for ($i = 1; $i <= 5; $i++) {
                if (isset($starsCountArray[$i])) {
                    $starsCountArray[$i]['avg'] = $starsCountArray[$i]['total'] / $reviewsCountTotal;
                } else {
                    $starsCountArray[$i] = array('total' => 0, 'avg' => 0);
                }
            }
            $avg = $sum / $reviewsCountTotal;
        }

        $reviews = $this->getReviews($itemID, $offset, $limit, $filter_type = 1);

        $data = array(
            'productId' => $itemID,
            'productName' => $this->getItemName($item),
            'productImage' => $this->getItemImageUrl($item),
            'productSku' => '',
            'productDescription' => $this->getItemDesc($item),
            'reviewsLimit' => $limit,
            'reviewsCountTotal' => $reviewsCountTotal,
            'reviewsCountPage' => count($reviews),
            'avgStars' => $avg,
            'starsCountArray' => $starsCountArray,
            'reviews' => $reviews,
            'noReviewText' => $this->configHelper->getNoReviewTxt(),
            'baseUrl' => 'base-url',
        );

        $this->getLogger(__FUNCTION__)->error('EkomiFeedback::ReviewsRepository.getReviewsContainerStats', $data);
        return $data;
    }

    public function getReviews($itemID, $offset, $limit, $filter_type) {
        $orderBy = $this->resolveOrderBy($filter_type);

        $result = $this->db->query(Reviews::class)
                        ->whereIn('productId', explode(',', $itemID))
                        ->where('shopId', '=', $this->configHelper->getShopId())
                        ->limit($limit)
                        ->orderBy($orderBy['fieldName'], $orderBy['direction'])
                        ->offset($offset)->get();
        return $result;
    }

    public function rateReview($itemID, $reviewId, $helpfulness) {
        $column = $helpfulness == '1' ? 'helpful' : 'nothelpful';

        $review = $this->db->find(Reviews::class, $reviewId);

        if ($helpfulness == '1') {
            $review->helpful = 1 + $review->helpful;
        } else {
            $review->nothelpful = 1 + $review->nothelpful;
        }

        $review = $this->db->save($review);

        return $review;
    }

    /**
     * 
     * @param int $filter_type The sorting filter value
     * 
     * @return string The Sorting filter
     */
    public function resolveOrderBy($filter_type) {
        $orderBy = array('fieldName' => 'id', 'direction' => 'asc');

        switch ($filter_type) {
            case 1:
                $orderBy['fieldName'] = 'id';
                $orderBy['direction'] = 'desc';
                break;
            case 2:
                $orderBy['fieldName'] = 'id';
                $orderBy['direction'] = 'asc';
                break;
            case 3:
                $orderBy['fieldName'] = 'helpful';
                $orderBy['direction'] = 'desc';
                break;
            case 4:
                $orderBy['fieldName'] = 'stars';
                $orderBy['direction'] = 'desc';
                break;
            case 5:
                $orderBy['fieldName'] = 'stars';
                $orderBy['direction'] = 'asc';
                break;

            default:
                break;
        }
        return $orderBy;
    }

    public function getItemIDs($item) {
        if (isset($item['item']['id'])) {
            return trim($item['item']['id']);
        }
        return NULL;
//        $this->getLogger(__FUNCTION__)->error('EkomiFeedback::ReviewsRepository.getReviewsStats', $item['item']);
    }

    public function getItemDesc($item) {
        if (isset($item['texts']['description'])) {
            if (empty($item['texts']['shortDescription'])) {
                return $item['texts']['description'];
            }
            return $item['texts']['shortDescription'];
        }
        return '';
    }

    public function getItemName($item) {
        if (isset($item['texts']['name1'])) {
            return $item['texts']['name1'];
        }
        return '';
    }

    public function getItemImageUrl($item) {
        if (isset($item['images']['all'][0])) {
            return $item['images']['all'][0]['urlPreview'];
        }
        return '';
    }

    public function getItemVarNumber($item) {
        if (isset($item['variation']['number'])) {
            return $item['variation']['number'];
        }
        return '';
    }

}
