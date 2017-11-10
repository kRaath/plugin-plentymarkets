<?php

namespace EkomiFeedback\Models;

use Plenty\Modules\Plugin\DataBase\Contracts\Model;

/**
 * Class Reviews
 *
 * @property int     $id
 * @property int     $shopId
 * @property string  $orderId
 * @property string  $productId
 * @property int     $timestamp
 * @property int     $stars
 * @property string  $reviewComment
 * @property int     $helpful
 * @property int     $nothelpful
 */
class Reviews extends Model {

    /**
     * @var int
     */
    public $id;
    public $shopId = 0;
    public $orderId = '';
    public $productId = '';
    public $timestamp = 0;
    public $stars = 0;
    public $reviewComment = '';
    public $helpful = 0;
    public $nothelpful = 0;

    /**
     * @return string
     */
    public function getTableName(): string {
        return 'EkomiFeedback::Reviews';
    }

}
