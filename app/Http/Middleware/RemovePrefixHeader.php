<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RemovePrefixHeader
{
    private array $unwantedHeaderList = [
        'x-forwarded-prefix'
    ];

    private array $unwantedServerVariablesList = [
        'HTTP_X_FORWARDED_PREFIX',
        'REQUEST_URI',
        'CONTEXT_PREFIX',
        'REDIRECT_URL'
    ];

    private $patternToRemove = '/^\/picasso/';
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->removeUnwantedHeaders($request, $this->unwantedHeaderList,$this->unwantedServerVariablesList);

        return $next($request);
    }

    private function removeUnwantedHeaders(Request $request,$headerList,$serverList): void
    {
        foreach ($headerList as $header) {
            $value = $request->headers->get($header);
            $value = preg_replace($this->patternToRemove, '', $value);
            $request->headers->set($header,$value);
        }
        foreach ($serverList as $serverVar) {
            $value = $request->server->get($serverVar);
            $value = preg_replace($this->patternToRemove, '', $value);
            $request->server->set($serverVar,$value);
        }
    }
}
