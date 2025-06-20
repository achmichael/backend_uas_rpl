<?php


use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('web', [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\Http\Middleware\TrustProxies::class,
        ]);

        $middleware->group('api', [
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Http\Middleware\TrustProxies::class,
        ]);

        $middleware->alias([
            'auth'             => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session'     => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers'    => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can'              => \Illuminate\Auth\Middleware\Authorize::class,
            'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'signed'           => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

    })->create();
