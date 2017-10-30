<?php
 
namespace EkomiFeedback\Migrations;
 
use EkomiFeedback\Models\EkomiFeedbackReviews;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
 
/**
 * Class CreateTable
 */
class CreateTable
{
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $migrate->createTable(EkomiFeedbackReviews::class);
    }
}