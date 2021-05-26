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
        try {
            $datetime = Carbon::createFromFormat('Y-m-d', $deprecatedAt)->startOfDay();
        } catch (\Exception $e) {
            throw new \Exception('Deprecate date has to be a date string in Y-m-d format.', 0, $e);
        }

        return $datetime->toIso8601String();
    }
}
