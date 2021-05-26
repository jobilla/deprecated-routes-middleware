<?php

namespace Jobilla\DeprecatedRoutes\Events;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RouteIsDeprecated
{
    public Request $request;
    public Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
