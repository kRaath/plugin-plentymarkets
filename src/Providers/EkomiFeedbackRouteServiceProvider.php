<?php

namespace EkomiFeedback\Providers;

use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;

/**
 * Class EkomiFeedbackRouteServiceProvider
 * @package EkomiFeedback\Providers
 */
class EkomiFeedbackRouteServiceProvider extends RouteServiceProvider {

    /**
     * @param Router $router
     */
    public function map(Router $router) {
        $router->get('sendOrdersToEkomi', 'EkomiFeedback\Controllers\ContentController@sendOrdersToEkomi');
        $router->get('fetchProductReviews', 'EkomiFeedback\Controllers\ContentController@fetchProductReviews');
        $router->get('reviews/{pwd}', 'EkomiFeedback\Controllers\ContentController@showReview');
        /**
         * Routes for ajax calls
         */
        $router->post('loadReviews', 'EkomiFeedback\Controllers\ContentController@loadReviews');
        $router->post('saveFeedback', 'EkomiFeedback\Controllers\ContentController@saveFeedback');
    }

}
