<?php

namespace EkomiFeedback\Containers;

use Plenty\Plugin\Templates\Twig;
use EkomiFeedback\Repositories\ReviewsRepository;

class EkomiFeedbackReviewsContainer {

    public function call(Twig $twig, $arg): string {
        $productID = 'omair33330';

        $offset = 0;
        $limit = 5;

        $reviewRepo = $database = pluginApp(ReviewsRepository::class);

        $data = $reviewRepo->getReviewsStats($arg[0],$productID, $offset, $limit);
        $data["item"] = $arg[0];

        return $twig->render('EkomiFeedback::content.reviewsContainer', $data);
    }

}
