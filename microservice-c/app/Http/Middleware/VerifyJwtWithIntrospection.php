<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;

class VerifyJwtWithIntrospection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        $accessToken = $request->bearerToken();
        $accessTokenType = 'access_token';

        $oktaDomain = env('OKTA_DOMAIN');
        $oktaClientId = env('OKTA_CLIENT_ID');

        try {
            // make api call to introspect endpoint
            $introspectionResponse = Http::asForm()->post("$oktaDomain/oauth2/default/v1/introspect?client_id=$oktaClientId", [
                'token' => $accessToken,
                'token_type_hint' => $accessTokenType
            ]);

            $isTokenActive = $introspectionResponse->json('active');

            if (!$isTokenActive) {
                throw new UnauthorizedException('token is invalid');
            }

        } catch (\Exception $exception) {
            Log::error($exception);
            return new Response('Unauthorized - Token failed Introspection', 401);
        }

        return $next($request);
    }
}
