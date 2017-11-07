<?php

namespace EkomiFeedback\Containers;

use Plenty\Plugin\Templates\Twig;

class EkomiFeedbackMiniStarsWidget {

    public function call(Twig $twig, \EkomiFeedback\Repositories\ReviewsRepository $reviewRepo): string {
        $avg = $reviewRepo->getAvgRating('ZUBFER-144');
        $count = $reviewRepo->getReviewsCount('ZUBFER-144');
        $itemTitle = 'Abc Item';

        $templateData = array("reviewsCount" => $count, "avgRating" => $avg, 'articleName' => $itemTitle);
        return $twig->render('EkomiFeedback::content.miniStarsWidget', $templateData);
    }

}
