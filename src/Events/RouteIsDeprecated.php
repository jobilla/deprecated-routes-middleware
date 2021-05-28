<?php

namespace Jobilla\DeprecatedRoutes\Events;

class RouteIsDeprecated
{
    /**
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * @var \Illuminate\Http\Response
     */
    public $response;

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
    }
}
