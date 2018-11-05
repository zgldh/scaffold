<?php

namespace App\Http\Middleware;

use Closure;
use League\Csv\Writer;

class GraphiQL
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
        if ($request->path() === 'graphiql') {
            if (env('APP_DEBUG', false) === false) {
                // It is not debug. We should protect this page
                abort(404);
            }
            if (config('scaffold.enable_graph_ql') === false) {
                // GraphQL is not enabled
                abort(404);
            }
        }
        return $next($request);
    }
}
