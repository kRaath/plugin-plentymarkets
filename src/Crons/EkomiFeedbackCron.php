<?php

namespace EkomiFeedback\Crons;

use Plenty\Modules\Cron\Contracts\CronHandler as Cron;
use EkomiFeedback\Services\EkomiServices;
use Plenty\Plugin\Log\Loggable;

/**
 * Class EkomiFeedbackCron
 */
class EkomiFeedbackCron extends Cron {

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

        $this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiFeedbackCron.handle', 'CronRunning....');

        $this->ekomiServices->sendOrdersData($daysDiff);
        $this->ekomiServices->fetchProductReviews($range = 'all');
    }

}
