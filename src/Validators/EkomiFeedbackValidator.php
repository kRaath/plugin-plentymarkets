<?php
 
namespace EkomiFeedback\Validators;
 
use Plenty\Validation\Validator;
 
/**
 *  Validator Class
 */
class EkomiFeedbackValidator extends Validator
{
    protected function defineAttributes()
    {
        $this->addString('shopId', true);
    }
}