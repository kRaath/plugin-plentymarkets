<?php
namespace EkomiFeedback\Providers;

use Plenty\Plugin\RouteServiceProvider;
use Plenty\Plugin\Routing\Router;

/**
 * Class EkomiFeedbackRouteServiceProvider
 * @package EkomiFeedback\Providers
 */
class EkomiFeedbackRouteServiceProvider extends RouteServiceProvider
{
	/**
	 * @param Router $router
	 */
	public function map(Router $router)
	{
		$router->get('sendOrdersToEkomi', 'EkomiFeedback\Controllers\ContentController@sendOrdersToEkomi');
	}

}
