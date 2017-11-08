<?php

namespace EkomiFeedback\Containers;

use Plenty\Plugin\Templates\Twig;

class EkomiFeedbackReviewsContainer {

    public function call(Twig $twig): string {
        $productID = 'omair33330';

        $offset = 0;
        $limit = 5;

        $reviewRepo = $database = pluginApp(ReviewsRepository::class);

        $data = $reviewRepo->getReviewsStats($productID, $offset, $limit);

        return $twig->render('EkomiFeedback::content.reviewsContainer', $data);
    }

}
