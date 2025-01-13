<?php

namespace App\Http\Middleware;

class ContentSecurityPolicy
{
    public function handle($request, $closure)
    {
        $response = $closure($request);

        $response->headers->set(
            'Content-Security-Policy',
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: http:;"
        );

        return $response;
    }
}