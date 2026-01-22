<?php

namespace App\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         $apiKey = $request->header('X-API-Key');
        
        if (!$apiKey) {
            return response()->json(['error' => 'API Key required'], 401);
        }
        
        $token = PersonalAccessToken::findToken($apiKey);
        
        if (!$token) {
            return response()->json(['error' => 'Invalid API Key'], 401);
        }
        
        auth()->login($token->tokenable);
        
        return $next($request);
    }
}
