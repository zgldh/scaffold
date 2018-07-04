<?php

namespace App\Http\Middleware;

use Closure;
use Modules\User\Repositories\PermissionRepository;


class AutoPermissionCheck
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
//        if (\Auth::guest()) {
//            return redirect('/');
//        }

        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        // Otherwise we need to check permissions

        $action = $request->route()->getAction();
        $actionName = preg_split('/@/', basename(str_replace('\\', '/', $action['uses'])));

        $moduleName = preg_replace('/Controller$/', '', $actionName[0]);
        $methodName = $actionName[1];

        if ($methodName == 'bundle') {
            $actionName = $request->input('action');
            if ($actionName == 'delete') {
                $actionName = 'destroy';
            }
            $permission = PermissionRepository::GENERATE_PERMISSION_CODE($moduleName, $actionName);
        } else {
            $permission = PermissionRepository::GENERATE_PERMISSION_CODE($moduleName, $actionName[1]);
        }

        \Log::debug("checking permission '{$permission}' to user " . $request->user()->id);

        if ($request->user()->hasPermissionTo($permission, 'api')) {
            return $next($request);
        }

        abort(403, __('auth.no_permission'));
    }
}
