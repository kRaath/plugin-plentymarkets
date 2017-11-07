<?php
 
namespace EkomiFeedback\Containers;
 
use Plenty\Plugin\Templates\Twig;
 
class EkomiFeedbackReviewsContainerTab
{
    public function call(Twig $twig):string
    {
        return $twig->render('EkomiFeedback::content.reviewsContainerTab');
    }
}