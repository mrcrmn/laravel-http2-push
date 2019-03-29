<?php

namespace mrcrmn\Http2Push\Middleware;

use Closure;

class AttachPushHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $http2push = resolve('http2push');

        if ($request->method() === 'GET' && $http2push->any()) {
            $response->header('Link', $http2push->generateHeader());
        }

        return $response;
    }
}
