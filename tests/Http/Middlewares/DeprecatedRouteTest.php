<?php

namespace Tests\Http\Middlewares;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Jobilla\DeprecatedRoutes\Events\RouteIsDeprecated;
use Jobilla\DeprecatedRoutes\Http\Middlewares\DeprecatedRoute;
use Tests\TestCase;

class DeprecatedRouteTest extends TestCase
{
    /** @test */
    public function it_adds_header_to_response()
    {
        $middleware = new DeprecatedRoute();

        Config::set('deprecated_routes.log_level', false);
        Config::set('deprecated_routes.fire_event', false);

        $request = Request::create('/', 'GET');
        $response = new Response('');

        $deprecatedAt = '2020-10-06';

        $middleware->handle($request, function ($request) use ($response) {
            return $response;
        }, $deprecatedAt);

        $this->assertTrue($response->headers->has(DeprecatedRoute::DEFAULT_HEADER_NAME));

        $this->assertEquals(
            Carbon::createFromFormat('Y-m-d', $deprecatedAt)->startOfDay()->toIso8601String(),
            $response->headers->get(DeprecatedRoute::DEFAULT_HEADER_NAME)
        );
    }

    /** @test */
    public function it_does_not_fire_an_event_when_option_is_disabled()
    {
        $middleware = new DeprecatedRoute();

        Config::set('deprecated_routes.log_level', false);
        Config::set('deprecated_routes.fire_event', false);

        $request = Request::create('/', 'GET');
        $response = new Response('');

        $deprecatedAt = '2020-10-06';

        Event::fake();

        $middleware->handle($request, function ($request) use ($response) {
            return $response;
        }, $deprecatedAt);

        Event::assertNotDispatched(RouteIsDeprecated::class);
    }

    /** @test */
    public function it_fires_an_event_when_option_is_enabled()
    {
        $middleware = new DeprecatedRoute();

        Config::set('deprecated_routes.log_level', false);
        Config::set('deprecated_routes.fire_event', true);

        $request = Request::create('/', 'GET');
        $response = new Response('');

        $deprecatedAt = '2020-10-06';

        Event::fake();

        $middleware->handle($request, function ($request) use ($response) {
            return $response;
        }, $deprecatedAt);

        Event::assertDispatchedTimes(RouteIsDeprecated::class, 1);
        Event::assertDispatched(RouteIsDeprecated::class, function (RouteIsDeprecated $event) use ($request, $response) {
            return $request === $event->request && $response === $event->response;
        });
    }

    /** @test */
    public function it_does_not_log_to_output_when_option_is_disabled()
    {
        $middleware = new DeprecatedRoute();

        Config::set('deprecated_routes.log_level', false);
        Config::set('deprecated_routes.fire_event', false);

        $request = Request::create('/', 'GET');
        $response = new Response('');

        $deprecatedAt = '2020-10-06';

        Log::shouldReceive('log')->never();

        $middleware->handle($request, function ($request) use ($response) {
            return $response;
        }, $deprecatedAt);
    }

    /** @test */
    public function it_logs_to_output_when_option_is_enabled()
    {
        $middleware = new DeprecatedRoute();

        Config::set('deprecated_routes.log_level', 'debug');
        Config::set('deprecated_routes.fire_event', false);

        $request = Request::create('/', 'GET');
        $response = new Response('');

        $deprecatedAt = '2020-10-06';

        Log::shouldReceive('log')->withSomeOfArgs('debug')->once();

        $middleware->handle($request, function ($request) use ($response) {
            return $response;
        }, $deprecatedAt);
    }

    /** @test */
    public function it_should_use_the_config_option_for_header_name()
    {
        $middleware = new DeprecatedRoute();

        Config::set('deprecated_routes.log_level', false);
        Config::set('deprecated_routes.fire_event', false);
        Config::set('deprecated_routes.header_name', 'This-Endpoint-Is-Discouraged-Since');

        $request = Request::create('/', 'GET');
        $response = new Response('');

        $deprecatedAt = '2020-10-06';

        $middleware->handle($request, function ($request) use ($response) {
            return $response;
        }, $deprecatedAt);

        $this->assertTrue($response->headers->has('This-Endpoint-Is-Discouraged-Since'));

        $this->assertEquals(
            Carbon::createFromFormat('Y-m-d', $deprecatedAt)->startOfDay()->toIso8601String(),
            $response->headers->get('This-Endpoint-Is-Discouraged-Since')
        );
    }
}
