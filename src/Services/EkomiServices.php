<?php

namespace EkomiFeedback\Services;

use EkomiFeedback\Helper\EkomiHelper;
use EkomiFeedback\Helper\ConfigHelper;
use EkomiFeedback\Repositories\OrderRepository;
use EkomiFeedback\Repositories\ReviewsRepository;
use Plenty\Plugin\ConfigRepository;
use Plenty\Plugin\Log\Loggable;

/**
 * Class EkomiServices
 */
class EkomiServices {

    use Loggable;

    /**
     * @var ConfigRepository
     */
    private $configHelper;
    private $ekomiHelper;
    private $orderRepository;
    private $reviewsRepository;

    public function __construct(ConfigHelper $configHelper, OrderRepository $orderRepo, ReviewsRepository $ekomiReviewsRepo, EkomiHelper $ekomiHelper) {
        $this->configHelper = $configHelper;
        $this->ekomiHelper = $ekomiHelper;
        $this->orderRepository = $orderRepo;
        $this->reviewsRepository = $ekomiReviewsRepo;
    }

    /**
     * Validates the shop
     * 
     * @return boolean True if validated False otherwise
     */
    public function validateShop() {
        $ApiUrl = 'http://api.ekomi.de/v3/getSettings';

        $ApiUrl .= "?auth={$this->configHelper->getShopId()}|{$this->configHelper->getShopSecret()}";
        $ApiUrl .= '&version=cust-1.0.0&type=request&charset=iso';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);

        $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiServices.validateShop', 'server_output:' . $server_output);

        if ($server_output == 'Access denied') {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /**
     * Sends orders data to eKomi System
     */
    public function sendOrdersData($daysDiff = 7) {

        if ($this->configHelper->getEnabled() == 'true') {
            if ($this->validateShop()) {

                $orderStatuses = $this->configHelper->getOrderStatus();
                $referrerIds = $this->configHelper->getReferrerIds();
                $plentyIDs = $this->configHelper->getPlentyIDs();

                $pageNum = 1;

                $fetchOrders = TRUE;

                while ($fetchOrders) {
                    $orders = $this->orderRepository->getOrders($pageNum);
                    // return $orders;
                    $flag = FALSE;
                    if (!empty($orders)) {
                        foreach ($orders as $key => $order) {

                            $plentyID = $order['plentyId'];
                            $referrerId = $order['orderItems'][0]['referrerId'];

                            if (!$plentyIDs || in_array($plentyID, $plentyIDs)) {

                                if (!empty($referrerIds) && in_array((string) $referrerId, $referrerIds)) {
                                    $this->getLogger(__FUNCTION__)->error(
                                            'EkomiFeedback::EkomiServices.sendOrdersData', 'Referrer ID :' . $referrerId .
                                            ' Blocked in plugin configuration.'
                                    );
                                    continue;
                                }

                                $updatedAt = $this->ekomiHelper->toMySqlDateTime($order['updatedAt']);

                                $orderId = $order['id'];

                                $statusId = $order['statusId'];

                                $orderDaysDiff = $this->ekomiHelper->daysDifference($updatedAt);

                                if ($orderDaysDiff <= $daysDiff) {

                                    if (in_array($statusId, $orderStatuses)) {

                                        $postVars = $this->ekomiHelper->preparePostVars($order);

                                        $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiServices.sendOrdersData', $postVars);
                                        // sends order data to eKomi
                                        $this->addRecipient($postVars, $orderId);
                                    }

                                    $flag = TRUE;
                                }
                            } else {
                                $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiServices.sendOrdersData', 'plenty ID not matched :' . $plentyID . '|' . implode(',', $plentyIDs));
                            }
                        }
                    }
                    //check to fetch next page
                    if ($flag) {
                        $fetchOrders = TRUE;
                        $pageNum++;
                    } else {
                        $fetchOrders = FALSE;
                    }
                }
            } else {
                $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiServices.sendOrdersData', 'Shop id or shop secret is not correct!');
            }
        } else {
            $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiServices.sendOrdersData', 'Plugin is not enabled!');
        }
    }

    /**
     * Calls the addRecipient API
     * 
     * @param string $postVars
     * 
     * @return string return the api status
     */
    public function addRecipient($postVars, $orderId = '') {
        if ($postVars != '') {
            $logMessage = "OrderID: {$orderId} => ";
            /*
             * The Api Url
             */
            $apiUrl = 'https://srr.ekomi.com/add-recipient';

            $boundary = md5('' . time());
            /*
             * Send the curl call
             */
            try {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $apiUrl);
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('ContentType:multipart/form-data;boundary=' . $boundary));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
                $exec = curl_exec($ch);
                curl_close($ch);

                $decodedResp = json_decode($exec);

                if ($decodedResp && $decodedResp->status == 'error') {
                    $logMessage .= $exec;
                    $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiServices.addRecipient', $logMessage);
                }
                return TRUE;
            } catch (\Exception $e) {
                $logMessage .= $e->getMessage();
                $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiServices.addRecipient', $logMessage);
            }
        }
        return FALSE;
    }

    /**
     * Fetches Product Reviews by Calling eKomi Api
     * 
     * @param string $range
     * @return Null
     */
    public function fetchProductReviews($range = 'all') {

        if ($this->configHelper->getEnabled() == 'true') {
            if ($this->validateShop()) {
                $review = $this->reviewsRepository->getReviewById(1);
                if (is_null($review)) {
                    $range = 'all';
                }

                $ekomi_api_url = "http://api.ekomi.de/v3/getProductfeedback?interface_id={$this->configHelper->getShopId()}&interface_pw={$this->configHelper->getShopSecret()}&type=json&charset=utf-8&range={$range}";
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ekomi_api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $product_reviews = curl_exec($ch);
                curl_close($ch);

                // log the results
                if ($product_reviews) {
                    $reviews = json_decode($product_reviews, true);

                    if ($reviews) {
                        $this->reviewsRepository->saveReviews($reviews);
                        $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiServices.fetchProductReviews', 'Reviews fetched  successfully. |url:' . $ekomi_api_url);
                    } else {
                        $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiServices.fetchProductReviews', 'Something went wrong! |url:' . $ekomi_api_url);
                    }
                } else {
                    $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiServices.fetchProductReviews', 'no reviews available. |url:' . $ekomi_api_url);
                }
            } else {
                $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiServices.fetchProductReviews', 'Shop id or shop secret is not correct! |url:' . $ekomi_api_url);
            }
        } else {
            $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiServices.fetchProductReviews', 'Plugin is not enabled!');
        }
        return NULL;
    }

}
