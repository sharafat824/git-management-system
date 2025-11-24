<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleGitHubWebhook
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Temporarily disable CSRF for GitHub webhooks
        $original = $request->attributes->get('_middleware');

        // Remove VerifyCsrfToken from middleware stack for this request
        if (is_array($original)) {
            $filtered = array_filter($original, function ($middleware) {
                return !str_contains($middleware, 'VerifyCsrfToken');
            });
            $request->attributes->set('_middleware', $filtered);
        }

        return $next($request);
    }
}
