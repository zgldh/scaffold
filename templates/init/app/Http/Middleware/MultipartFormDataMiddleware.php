<?php namespace App\Http\Middleware;

use Closure;

class MultipartFormDataMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->files->count()) {
            $all = $request->input();
            foreach ($all as $key => $val) {
                $all[$key] = json_decode($val);
            }
            $request->replace($all);
        }
        return $next($request);
    }
}
