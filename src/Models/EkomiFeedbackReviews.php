<?php
 
namespace EkomiFeedback\Models;
 
use Plenty\Modules\Plugin\DataBase\Contracts\Model;
 
/**
 * Class EkomiFeedbackReviews
 *
 * @property int     $id
 * @property string  $taskDescription
 * @property int     $userId
 * @property boolean $isDone
 * @property int     $createdAt
 */
class EkomiFeedbackReviews extends Model
{
    /**
     * @var int
     */
    public $id              = 0;
    public $taskDescription = '';
    public $userId          = 0;
    public $isDone          = false;
    public $createdAt       = 0;
 
    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'EkomiFeedback::EkomiFeedbackReviews';
    }
}