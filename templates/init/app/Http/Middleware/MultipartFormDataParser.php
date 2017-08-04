<?php namespace App\Http\Middleware;

use Closure;

class MultipartFormDataParser
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
        if ($request->has('_data')) {
            $data = json_decode($request->get('_data'), true);
            $request->merge($data);
        }

        return $next($request);
    }
}
