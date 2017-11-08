<?php

namespace EkomiFeedback\Containers;

use Plenty\Plugin\Templates\Twig;
use EkomiFeedback\Repositories\ReviewsRepository;

class EkomiFeedbackMiniStarsWidget {

    public function call(Twig $twig, $arg): string {
        $productID = 'omair33330';

        $reviewRepo = $database = pluginApp(ReviewsRepository::class);
        $avg = $reviewRepo->getAvgRating($productID);
        $count = $reviewRepo->getReviewsCount($productID);
        $itemTitle = 'Abc Item';

        $templateData = array("reviewsCount" => $count, "avgRating" => $avg, 'articleName' => $itemTitle);
        return $twig->render('EkomiFeedback::content.miniStarsWidget', $templateData);
    }

}
