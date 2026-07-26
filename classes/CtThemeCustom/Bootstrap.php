<?php

namespace CtThemeCustom;

use Elgg\DefaultPluginBootstrap;

class Bootstrap extends DefaultPluginBootstrap {
    
    /**
     * Register hook handlers
     */
    public function boot() {
        $this->elgg()->events->registerHandler('prepare', 'notification', [GlobalEmailHandler::class, 'prepareEmailNotification']);
    }
}