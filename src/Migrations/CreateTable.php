<?php
 
namespace EkomiFeedback\Migrations;
 
use EkomiFeedback\Models\EkomiFeedbackReviews;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;
/**
 * Class CreateTable
 */
class CreateTable
{
    use Loggable;
    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate)
    {
        $this->getLogger(__FUNCTION__)->error('EkomiFeedback::CreateTable.run', $migrate->deleteTable(EkomiFeedbackReviews::class));
        $this->getLogger(__FUNCTION__)->error('EkomiFeedback::CreateTable.run', $migrate->createTable(EkomiFeedbackReviews::class));
    }
}