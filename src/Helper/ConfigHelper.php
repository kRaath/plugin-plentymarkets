<?php

namespace EkomiFeedback\Helper;

use Plenty\Plugin\ConfigRepository;

/**
 * Class ConfigHelper
 */
class ConfigHelper {

    /**
     * @var ConfigRepository
     */
    private $config;

    public function __construct(ConfigRepository $config)
    {
        $this->config = $config;
    }

    public function getEnabled()
    {
        return $this->config->get('EkomiFeedback.is_active');
    }

    public function getMod()
    {
        return $this->config->get('EkomiFeedback.mode');
    }

    public function getShopId()
    {
        $shopId = $this->config->get('EkomiFeedback.shop_id');

        return preg_replace('/\s+/', '', $shopId);
    }

    public function getPlentyIDs()
    {
        $plentyIDs = false;

        $IDs = $this->config->get('EkomiFeedback.plenty_IDs');

        $IDs = preg_replace('/\s+/', '', $IDs);

        if (!empty($IDs)) {
            $plentyIDs = explode(',', $IDs);
        }

        return $plentyIDs;
    }

    public function getShopSecret()
    {
        $secret = $this->config->get('EkomiFeedback.shop_secret');

        return preg_replace('/\s+/', '', $secret);
    }

    public function getProductReviews()
    {
        return $this->config->get('EkomiFeedback.product_reviews');
    }
    
    public function getGroupReviews()
    {
        return $this->config->get('EkomiFeedback.group_reviews');
    }
    
    public function getNoReviewTxt()
    {
        return $this->config->get('EkomiFeedback.no_review_text');
    }

    public function getOrderStatus()
    {
        $status = $this->config->get('EkomiFeedback.order_status');
        $statusArray = explode(',', $status);

        return $statusArray;
    }

    public function getReferrerIds()
    {
        $referrerIds = $this->config->get('EkomiFeedback.referrer_id');
        $referrerIds = explode(',', $referrerIds);

        return $referrerIds;
    }

}
