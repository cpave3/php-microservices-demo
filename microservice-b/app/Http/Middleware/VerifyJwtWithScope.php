<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Okta\JwtVerifier\Adaptors\FirebasePhpJwt;
use Okta\JwtVerifier\JwtVerifierBuilder;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class VerifyJwtWithScope
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

        $jwtVerifier = (new JwtVerifierBuilder())
            ->setAdaptor(new FirebasePhpJwt())
            ->setAudience(env('OKTA_AUDIENCE'))
            ->setClientId(env('OKTA_CLIENT_ID'))
            ->setIssuer(env('OKTA_ISSUER_URI'))
            ->build();


        // Microservice B verifies the token itself, and looks for a specific scope
        try {
            $jwt = $jwtVerifier->verify($request->bearerToken());

            $scopes = Arr::get($jwt->claims, 'scp', []);
            $requiredScope = 'microservice-demo-scope';

            if (!in_array($requiredScope, $scopes)) {
                throw new UnauthorizedHttpException('missing required scope');
            }

            return $next($request);
        } catch (\Exception $exception) {
            Log::error($exception);
        }

        return response('Unauthorized', 401);
    }
}
