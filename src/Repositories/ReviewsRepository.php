<?php

namespace EkomiFeedback\Repositories;

use Plenty\Modules\Plugin\DataBase\Contracts\DataBase;
use EkomiFeedback\Models\Reviews;
use Plenty\Modules\Frontend\Services\AccountService;
use EkomiFeedback\Helper\ConfigHelper;
use Plenty\Plugin\Log\Loggable;

class ReviewsRepository {

    use Loggable;

    private $db;
    private $configHelper;

    /**
     * UserSession constructor.
     * @param AccountService $accountService
     */
    public function __construct(ConfigHelper $configHelper, DataBase $db) {
        $this->configHelper = $configHelper;
        $this->db = $db;
    }

    public function getReviewsList($pwd) {
        if ($pwd == 'ekomi1@3') {
            /**
             * @var Reviews[] $ekomiReviewsList
             */
            $ekomiReviewsList = $this->db->query(Reviews::class)->where('shopId', '=', $this->configHelper->getShopId())->get();
        } else {
            $ekomiReviewsList = NULL;
        }
        return $ekomiReviewsList;
    }

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
        } else {
            $this->getLogger(__FUNCTION__)->error('EkomiFeedback::ReviewsRepository.getReviewsContainerStats', $item);
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
            'baseUrl' => $this->getBaseUrl($item)
        );
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

    public function getReviewById($reviewId) {
        $review = $this->db->query(Reviews::class)
                ->where('id', '=', $reviewId)
                ->get();

        if (isset($review[0])) {
            return $review[0];
        }

        return NULL;
    }

    public function rateReview($itemID, $reviewId, $helpfulness) {
        $review = $this->getReviewById($reviewId);

        if (!is_null($review)) {
            if ($helpfulness == '1') {
                $review->helpful = 1 + $review->helpful;
            } else {
                $review->nothelpful = 1 + $review->nothelpful;
            }
            $this->db->save($review);
            return $review;
        } else {
            return NULL;
        }
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

    public function getBaseUrl($item) {
        $url = $this->getItemImageUrl($item);
        if (!empty($url)) {
            $url = explode('item', $url);
            if (isset($url[0])) {
                return $url;
            }
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
