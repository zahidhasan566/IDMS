<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfNotSecure
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('app.env') === 'production') {
            if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
                $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $redirect);
                exit();
            }
        }
        return $next($request);
    }
}
