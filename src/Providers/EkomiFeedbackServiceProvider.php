<?php

namespace EkomiFeedback\Providers;

use Plenty\Plugin\ServiceProvider;
use Plenty\Modules\Cron\Services\CronContainer;
use EkomiFeedback\Crons\EkomiFeedbackCron;
use EkomiFeedback\Repositories\EkomiFeedbackReviewsRepository;
use EkomiFeedback\Services\EkomiServices;
use Plenty\Plugin\Log\Loggable;

/**
 * Class EkomiFeedbackServiceProvider
 * @package EkomiFeedback\Providers
 */
class EkomiFeedbackServiceProvider extends ServiceProvider {

    use Loggable;

    /**
     * Register the service provider.
     */
    public function register() {
        $this->getApplication()->register(EkomiFeedbackRouteServiceProvider::class);
        $this->getApplication()->bind(EkomiFeedbackReviewsRepository::class);

        $service = pluginApp(EkomiServices::class);
        $service->fetchProductReviews($range = '1w');
    }

    public function boot(CronContainer $container) {
        // register crons
        //EVERY_FIFTEEN_MINUTES | DAILY
        //$this->getLogger(__FUNCTION__)->error('EkomiFeedback::EkomiFeedbackServiceProvider.boot', 'CronRegistered');

        $container->add(CronContainer::EVERY_FIFTEEN_MINUTES, EkomiFeedbackCron::class);
    }

}
