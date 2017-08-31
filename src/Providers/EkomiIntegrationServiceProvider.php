<?php

namespace EkomiIntegration\Providers;

use Plenty\Plugin\ServiceProvider;
use Plenty\Modules\Cron\Services\CronContainer;
use EkomiIntegration\Crons\OrdersExportCron;
use Plenty\Plugin\Log\Loggable;
use Plenty\Plugin\ConfigRepository;
/**
 * Class EkomiIntegrationServiceProvider
 * @package EkomiIntegration\Providers
 */
class EkomiIntegrationServiceProvider extends ServiceProvider {
    
    use Loggable;
    
    /**
     * Register the service provider.
     */
    public function register() {
        $this->getApplication()->register(EkomiIntegrationRouteServiceProvider::class);
    }

    public function boot(CronContainer $container,ConfigHelper $configHelper) {
        // register crons
        //EVERY_FIFTEEN_MINUTES | DAILY
        
        $this->getLogger(__FUNCTION__)->error('EkomiIntegration::EkomiIntegrationServiceProvider.boot', 'CronDone');
        
        $plentyIDs = $configHelper->getPlentyIDs();
        
        $this->getLogger(__FUNCTION__)->error('EkomiIntegration::EkomiIntegrationServiceProvider.boot', 'PlentyID:'. implode(',', $plentyIDs));
        
        $container->add(CronContainer::EVERY_FIFTEEN_MINUTES, OrdersExportCron::class);
    }

}
