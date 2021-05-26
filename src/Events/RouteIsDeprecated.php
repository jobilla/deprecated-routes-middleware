<?php

namespace Jobilla\DeprecatedRoutes\Events;

use Illuminate\Routing\Route;

class RouteIsDeprecated
{
    private Route $route;

    public function __construct(Route $route)
    {
        $this->route = $route;
    }
}
