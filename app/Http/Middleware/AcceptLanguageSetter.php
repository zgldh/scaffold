<?php

namespace App\Http\Middleware;

use Closure;
use League\Csv\Writer;

class AcceptLanguageSetter
{
    private $map = [
        'zh-cn' => 'zh-CN',
        'zh'    => 'zh-CN'
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
        $acceptLanguage = $request->header('Accept-Language');
        $locale = $this->getLocale($acceptLanguage);
        \App::setLocale($locale);
        return $next($request);
    }

    private function getLocale($acceptLanguage)
    {
        if (str_contains($acceptLanguage, ',')) {
            $acceptLanguage = preg_split('/,/', $acceptLanguage)[0];
        }

        $locale = snake_case(strtolower($acceptLanguage));
        if (isset($this->map[$locale])) {
            return $this->map[$locale];
        }

        $langDirectory = resource_path('lang/' . $acceptLanguage);
        if (is_dir($langDirectory)) {
            return $acceptLanguage;
        }
        return config('app.locale');
    }
}
