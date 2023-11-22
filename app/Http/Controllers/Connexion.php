<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class Connexion extends Controller
{
    /**
     * Handle the authentication process.
     *
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function auth(Request $request): Redirector|RedirectResponse
    {
        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            "clientId" => config('app.OAUTH_clientId'),
            "clientSecret" => config('app.OAUTH_clientSecret'),
            "redirectUri" => config('app.OAUTH_redirectUri'),
            "urlAuthorize" => "https://auth.assos.utc.fr/oauth/authorize",
            "urlAccessToken" => "https://auth.assos.utc.fr/oauth/token",
            "urlResourceOwnerDetails" => "https://auth.assos.utc.fr/api/user",
            "scopes" => "users-infos read-assos read-memberships",
            "baseUrl" => "https://auth.assos.utc.fr/api/user",
        ]);

        // If the authorization code is not present, authenticate the user
        if (empty($request->input('code'))) {
            // Get the authorization URL
            $authUrl = $provider->getAuthorizationUrl();

            // Store the OAuth2 state in the session
            session(['oauth2state' => $provider->getState()]);
            Log::info('Storing provider state ' . session('oauth2state'));

            return redirect($authUrl);
        } else {
            try {
                // Exchange the authorization code for an access token
                $accessToken = $provider->getAccessToken('authorization_code', [
                    'code' => $request->input('code'),
                ]);

                // Create a cookie with the access token and set its expiration time to 1440 minutes (24 hours)
                $cookie = cookie(config('app.token_name'), $accessToken, 1440);

                $cookie_route = $request->cookie('route');
                // If a stored route is present, redirect to that route with the access token cookie
                if (!empty($cookie_route)) {
                    return redirect()->route($cookie_route)->withCookie($cookie);
                } else {
                    return redirect('/');
                }
            } catch (IdentityProviderException $e) {
                dd($e->getMessage());
            }
        }
    }
}
