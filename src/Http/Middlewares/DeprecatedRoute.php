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
    public const HEADER_NAME = 'X-Deprecated-At';

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

        $response->headers->set(static::HEADER_NAME, $this->getTimestampString($deprecatedAt));

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

        Log::log($level, 'Deprecated route on ' . $request->path() . ' was requested.');
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     */
    protected function fireDeprecationEvent($request, $response)
    {
        $enabled = Config::get('deprecated_routes.fire_event');

        if (!$enabled) {
            return;
        }

        Event::dispatch(new RouteIsDeprecated($request, $response));
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
            throw new MalformedParameterException('Deprecate date has to be a date string in Y-m-d format.', 0, $e);
        }

        return $datetime->toIso8601String();
    }
}
