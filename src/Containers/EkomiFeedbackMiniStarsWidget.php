<?php

namespace EkomiFeedback\Containers;

use Plenty\Plugin\Templates\Twig;
use EkomiFeedback\Repositories\ReviewsRepository;

class EkomiFeedbackMiniStarsWidget {

    public function call(Twig $twig, $arg): string {
        $reviewRepo = $database = pluginApp(ReviewsRepository::class);

        $templateData = $reviewRepo->getMiniStarsStats($arg[0]);

        return $twig->render('EkomiFeedback::content.miniStarsWidget', $templateData);
    }

}
