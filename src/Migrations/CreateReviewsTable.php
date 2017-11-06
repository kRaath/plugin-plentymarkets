<?php

namespace EkomiFeedback\Migrations;

use EkomiFeedback\Models\Reviews;
use Plenty\Modules\Plugin\DataBase\Contracts\Migrate;
use Plenty\Plugin\Log\Loggable;

/**
 * Class CreateReviewsTable
 */
class CreateReviewsTable {

    use Loggable;

    /**
     * @param Migrate $migrate
     */
    public function run(Migrate $migrate) {
        $migrate->createTable(Reviews::class);
    }

}
