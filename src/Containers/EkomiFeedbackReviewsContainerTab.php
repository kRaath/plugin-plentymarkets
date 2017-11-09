<?php

namespace EkomiFeedback\Containers;

use Plenty\Plugin\Templates\Twig;
use EkomiFeedback\Repositories\ReviewsRepository;

class EkomiFeedbackReviewsContainerTab {

    public function call(Twig $twig, $arg): string {
        $reviewRepo = $database = pluginApp(ReviewsRepository::class);

        $count = $reviewRepo->getReviewsCount($arg[0]);

        $templateData = array("reviewsCount" => $count);

        return $twig->render('EkomiFeedback::content.reviewsContainerTab', $templateData);
    }

}
