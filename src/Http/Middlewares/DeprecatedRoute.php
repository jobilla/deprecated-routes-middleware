<?php

namespace Jobilla\DeprecatedRoutes\Http\Middlewares;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Jobilla\DeprecatedRoutes\Events\RouteIsDeprecated;
use Jobilla\DeprecatedRoutes\Exceptions\MalformedParameterException;

class DeprecatedRoute
{
    /**
     * @var string
     */
    public const DEFAULT_HEADER_NAME = 'X-Deprecated-At';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  $deprecatedAt
     * @return mixed
     */
    public function handle($request, \Closure $next, $deprecatedAt)
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        $this->logDeprecatedRequest($request);
        $this->fireDeprecationEvent($request, $response);

        $response->headers->set($this->getHeaderName(), $this->getTimestampString($deprecatedAt));

        return $response;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     */
    protected function logDeprecatedRequest($request)
    {
        $level = Config::get('deprecated_routes.log_level');

        if ($level === false) {
            return;
        }

        Log::log($level, 'Deprecated route was requested.', ['path' => $request->path()]);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     */
    protected function fireDeprecationEvent($request, $response)
    {
        if (!Config::get('deprecated_routes.fire_event')) {
            return;
        }

        Event::dispatch(new RouteIsDeprecated($request, $response));
    }

    /**
     * @return string
     */
    protected function getHeaderName(): string
    {
        return Config::get('deprecated_routes.header_name', static::DEFAULT_HEADER_NAME) ?? static::DEFAULT_HEADER_NAME;
    }

    /**
     * @param  string  $deprecatedAt
     * @return string
     */
    protected function getTimestampString($deprecatedAt): string
    {
        try {
            $datetime = Carbon::createFromFormat('Y-m-d', $deprecatedAt)->startOfDay();
        } catch (\Exception $e) {
            throw new MalformedParameterException('Deprecation date must be a date string in Y-m-d format.', 0, $e);
        }

        return $datetime->toIso8601String();
    }
}
