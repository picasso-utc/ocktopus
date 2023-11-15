<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use League\OAuth2\Client\Token\AccessToken;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            "clientId" => "9a9d319d-1507-4bf9-9649-03f1f483df20",
            "clientSecret" => "1c0ad6d93e2998c8de87f62bfdb4657acb8ed6e1de3e3d80f6a7b5350fcf03e6",
            "redirectUri" => "http://localhost:8000/auth",
            "urlAuthorize" => "https://auth.assos.utc.fr/oauth/authorize",
            "urlAccessToken" => "https://auth.assos.utc.fr/oauth/token",
            "urlResourceOwnerDetails" => "https://auth.assos.utc.fr/api/user",
            "scopes" => "users-infos read-assos read-memberships",
            "baseUrl" => "https://auth.assos.utc.fr/api/user"
        ]);
        $token = $request->cookie(config('app.token_name'));
        if($token==null){
            $cookie_route=cookie('route',$request->route()->getName(),10);
            return redirect()->route('auth_route')->withCookie($cookie_route);
        }
        $access_token = new AccessToken(['access_token'=>$token]);
        /* Il faut encore réussir a test pour la validité du token
         * if($access_token->hasExpired()){
            $cookie_route=cookie('route',$request->route()->getName(),10);
            return redirect()->route('auth_route')->withCookie($cookie_route)->withoutCookie(config('app.token_name'));
        }
        */
        $resourceOwner = $provider->getResourceOwner($access_token);
        dd($resourceOwner);
        return $next($request);
    }
}
