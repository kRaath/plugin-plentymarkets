<?php

namespace EkomiFeedback\Containers;

use Plenty\Plugin\Templates\Twig;

use EkomiFeedback\Repositories\ReviewsRepository

class EkomiFeedbackMiniStarsWidget {

    public function call(Twig $twig, ReviewsRepository $reviewRepo): string {
        $avg = $reviewRepo->getAvgRating('ZUBFER-144');
        $count = $reviewRepo->getReviewsCount('ZUBFER-144');
        $itemTitle = 'Abc Item';

        $templateData = array("reviewsCount" => $count, "avgRating" => $avg, 'articleName' => $itemTitle);
        return $twig->render('EkomiFeedback::content.miniStarsWidget', $templateData);
    }

}
