<?php

namespace EkomiFeedback\Containers;

use Plenty\Plugin\Templates\Twig;

class EkomiFeedbackReviewsContainerTab {

    public function call(Twig $twig): string {
        $productID = 'omair33330';
        
        $reviewRepo = $database = pluginApp(ReviewsRepository::class);
        $count = $reviewRepo->getReviewsCount($productID);
        $templateData = array("reviewsCount" => $count);
        return $twig->render('EkomiFeedback::content.reviewsContainerTab', $templateData);
    }

}
