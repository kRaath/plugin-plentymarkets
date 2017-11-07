<?php

namespace EkomiFeedback\Containers;

use Plenty\Plugin\Templates\Twig;

class EkomiFeedbackMiniStarsWidget {

    public function call(Twig $twig): string {
        $templateData = array("reviewsCount" => 29, "avgRating" => 3.5, 'articleName' => 'Test');
        
        
        return $twig->render('EkomiFeedback::content.miniStarsWidget', $templateData);
    }

    public function getAvgRating() {
        return 3.7;
    }

}
