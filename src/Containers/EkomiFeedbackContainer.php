<?php
 
namespace EkomiFeedback\Containers;
 
use Plenty\Plugin\Templates\Twig;
 
class EkomiFeedbackContainer
{
    public function call(Twig $twig):string
    {
        return $twig->render('EkomiFeedback::content.hello');
    }
}