<?php

namespace EkomiIntegration\Crons;

use Plenty\Modules\Cron\Contracts\CronHandler as Cron;
use EkomiIntegration\Services\EkomiServices;

/**
 * Class OrdersExportCron
 */
class OrdersExportCron extends Cron {

    /**
     *
     * @var $ekomiServices 
     */
    private $ekomiServices;

    public function __construct(EkomiServices $ekomiService) {
        $this->ekomiServices = $ekomiService;
    }

    public function handle() {
        $daysDiff = 7;
        
        $this->getLogger(__FUNCTION__)->error('EkomiIntegration::OrdersExportCron.handle', 'CronRunning....');

        $this->ekomiServices->sendOrdersData($daysDiff);
    }

}
