<?php

namespace Jobilla\DeprecatedRoutes\Http\Middlewares;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
    public function handle(Request $request, \Closure $next, $deprecatedAt): Response
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        $response->header(static::HEADER_NAME, $this->getTimestampString($deprecatedAt));

        return $response;
    }

    protected function getTimestampString($deprecatedAt): string
    {
        if (is_string($deprecatedAt)) {
            $datetime = Carbon::createFromFormat('Y-m-d', $deprecatedAt);
        } elseif ($deprecatedAt instanceof Carbon) {
            $datetime = $deprecatedAt->startOfDay();
        } else {
            throw new \Exception('Deprecate date either has to be a Y-m-d date string or a Carbon instance.');
        }

        return $datetime->toIso8601String();
    }
}
