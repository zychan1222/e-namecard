<?php

namespace App\Http\Middleware;

use App\Models\Internationalization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GeneralJsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header_language = $request->headers->get('Accept-Language');

        // TODO: To change this to config
        $accepted_language = Internationalization::get()->pluck('code');

        if(!in_array($header_language, $accepted_language->toArray())){
            $locale = config('app.locale');
        } else {
            $locale = $header_language;
        }
        // Set locale to default if parameter is used by API
        app()->setLocale($locale);

        // If no accept header is set, set default as application/json
        $request->headers->set('Accept', 'application/json');

        // If no content type header is set, set default as application/json
        if(!$request->headers->has('Content-Type')){
            $request->headers->set('Content-Type', 'application/json');
        }

        return $next($request);
    }
}
