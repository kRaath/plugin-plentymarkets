<?php

namespace EkomiIntegration\Crons;

use Plenty\Modules\Cron\Contracts\CronHandler as Cron;
use EkomiIntegration\Services\EkomiServices;
use Plenty\Plugin\Log\Loggable;

/**
 * Class OrdersExportCron
 */
class OrdersExportCron extends Cron {

    use Loggable;

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
         
        $ApiUrl = 'http://plugindev.coeus-solutions.de/insert.php?value=CronRunning....';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        $this->ekomiServices->sendOrdersData($daysDiff);
    }

}
