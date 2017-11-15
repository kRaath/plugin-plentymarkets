<?php

namespace EkomiFeedback\Containers;

use Plenty\Plugin\Templates\Twig;
use EkomiFeedback\Repositories\ReviewsRepository;

class EkomiFeedbackMiniStarsCounter {

    public function call(Twig $twig, $arg): string {
        $reviewRepo = pluginApp(ReviewsRepository::class);

        $templateData = $reviewRepo->getMiniStarsStats($arg[0]);

        return $twig->render('EkomiFeedback::content.miniStarsCounter', $templateData);
    }

}
