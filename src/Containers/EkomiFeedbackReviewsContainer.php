<?php

namespace EkomiFeedback\Containers;

use Plenty\Plugin\Templates\Twig;
use EkomiFeedback\Repositories\ReviewsRepository;

/**
 * Ekomi Feedback Reviews Container
 */
class EkomiFeedbackReviewsContainer {

    public function call(Twig $twig, $arg): string {
        $offset = 0;
        $limit = 5;

        $reviewRepo = pluginApp(ReviewsRepository::class);

        $data = $reviewRepo->getReviewsContainerStats($arg[0], $offset, $limit);

        return $twig->render('EkomiFeedback::content.reviewsContainer', $data);
    }

}
