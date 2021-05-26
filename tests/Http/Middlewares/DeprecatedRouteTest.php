<?php

namespace Tests\Http\Middlewares;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Jobilla\DeprecatedRoutes\Http\Middlewares\DeprecatedRoute;
use PHPUnit\Framework\TestCase;

class DeprecatedRouteTest extends TestCase
{
    /** @test */
    public function it_adds_header_to_response()
    {
        $middleware = new DeprecatedRoute();

        $request = Request::create('/', 'GET');
        $response = new Response('');

        $deprecatedAt = '2020-10-06';

        $middleware->handle($request, function ($request) use ($response) {
            return $response;
        }, $deprecatedAt);

        $this->assertTrue($response->headers->has(DeprecatedRoute::HEADER_NAME));
        $this->assertEquals(Carbon::createFromFormat('Y-m-d', $deprecatedAt)->toIso8601String(), $response->headers->get(DeprecatedRoute::HEADER_NAME));
    }
}
