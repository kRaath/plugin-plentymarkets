<?php

namespace EkomiFeedback\Controllers;

use Plenty\Plugin\Controller;
use Plenty\Plugin\Templates\Twig;
use EkomiFeedback\Services\EkomiServices;

/**
 * Class ContentController
 * @package EkomiFeedback\Controllers
 */
class ContentController extends Controller {

    /**
     * @param Twig $twig
     * @return string
     */
    public function sendOrdersToEkomi(Twig $twig, EkomiServices $service): string {

        $service->sendOrdersData(7);

        return $twig->render('EkomiFeedback::content.hello');
    }

}
