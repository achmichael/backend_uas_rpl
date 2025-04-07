<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ForceHttps
{
    public function handle(Request $request, Closure $next)
    {
        // Force URLs to use HTTPS
        URL::forceScheme('https');
        
        // If you're behind Cloudflare, you might also need to trust the proxy
        $request->setTrustedProxies(
            [$request->getClientIp()], 
            Request::HEADER_X_FORWARDED_ALL
        );
        
        return $next($request);
    }
}
