<?php

namespace EkomiFeedback\Containers;

use Plenty\Plugin\Templates\Twig;
use EkomiFeedback\Repositories\ReviewsRepository;

class EkomiFeedbackMiniStarsWidget {

    public function call(Twig $twig): string {
        $reviewRepo = $database = pluginApp(ReviewsRepository::class);
        $avg = $reviewRepo->getAvgRating('omair33330');
        $count = $reviewRepo->getReviewsCount(array('omair33330'));
        $itemTitle = 'Abc Item';

        $templateData = array("reviewsCount" => $count, "avgRating" => $avg, 'articleName' => $itemTitle);
        return $twig->render('EkomiFeedback::content.miniStarsWidget', $templateData);
    }

}
