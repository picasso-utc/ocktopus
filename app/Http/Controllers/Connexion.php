<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class Connexion extends Controller
{
    public function auth(Request $request){
        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            "clientId" => config('app.OAUTH_clientId'),
            "clientSecret" => config('app.OAUTH_clientSecret'),
            "redirectUri" => config('app.OAUTH_redirectUri'),
            "urlAuthorize" => "https://auth.assos.utc.fr/oauth/authorize",
            "urlAccessToken" => "https://auth.assos.utc.fr/oauth/token",
            "urlResourceOwnerDetails" => "https://auth.assos.utc.fr/api/user",
            "scopes" => "users-infos read-assos read-memberships",
            "baseUrl" => "https://auth.assos.utc.fr/api/user"
        ]);
        if(empty($request->input('code'))) {
            // If we don't have an authorization code then get one
            $authUrl = $provider->getAuthorizationUrl();
            session(['oauth2state', $provider->getState()]);
            Log::info('Storing provider state ' . session('oauth2state'));
            return redirect($authUrl);
        }else{
            try {
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $request->input('code')
                ]);
                $cookie=cookie(config('app.token_name'),$accessToken,1440);
                $cookie_route=$request->cookie('route');
                if(!empty($cookie_route)){
                    return redirect()->route($cookie_route)->withCookie($cookie);
                }else{
                    return redirect('/');
                }
            } catch (IdentityProviderException $e) {
                dd($e->getMessage());
            }
        }
    }
}
