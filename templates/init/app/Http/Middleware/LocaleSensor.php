<?php namespace App\Http\Middleware;

use Closure;

class LocaleSensor
{
    /**
     * @var array
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('_locale')) {
            \App::setLocale($request->get('_locale'));
        } elseif ($request->hasHeader('Locale')) {
            \App::setLocale($request->header('Locale'));
        }

        return $next($request);
    }
}
