<?php

namespace Jobilla\DeprecatedRoutes;

use Illuminate\Support\ServiceProvider;

class DeprecatedRoutesProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/deprecated_routes.php', 'deprecated_routes');
    }
}
