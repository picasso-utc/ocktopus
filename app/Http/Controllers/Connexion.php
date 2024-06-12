<?php

namespace App\Http\Controllers;

use App\Enums\MemberRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class Connexion extends Controller
{
    /**
     * Handle the authentication process.
     *
     * @param  Request $request
     * @return mixed
     */
    public function auth(Request $request): mixed
    {
        $provider = new \League\OAuth2\Client\Provider\GenericProvider(
            [
            "clientId" => config('app.OAUTH_clientId'),
            "clientSecret" => config('app.OAUTH_clientSecret'),
            "redirectUri" => config('app.OAUTH_redirectUri'),
            "urlAuthorize" => "https://auth.assos.utc.fr/oauth/authorize",
            "urlAccessToken" => "https://auth.assos.utc.fr/oauth/token",
            "urlResourceOwnerDetails" => "https://auth.assos.utc.fr/api/user",
            "scopes" => "users-infos read-assos read-memberships",
            "baseUrl" => "https://auth.assos.utc.fr/api/user",
            ]
        );

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
                $accessToken = $provider->getAccessToken(
                    'authorization_code',
                    [
                    'code' => $request->input('code'),
                    ]
                );

                $userData = $provider->getResourceOwner($accessToken);
                // Make a request to the authentication server to get user associations
                $response = Http::withToken($accessToken)->get('https://auth.assos.utc.fr/api/user/associations/current');

                if ($response->failed()) {
                    return response()->json(['message' => 'Error while getting user infos','JWT_ERROR' => true], 401);
                }

                $userAssos = $response->json();

                $adminStatus = MemberRole::None;
                // Check if the user is a member or administrator of the picasso
                foreach ($userAssos as $asso) {
                    if ($asso['login'] == 'picasso') {
                        $adminStatus = MemberRole::Member;
                        $acceptedRoles = ['developer', 'resp informatique', 'president', 'treasury', 'vice-treasury', 'secretaire general', 'vice-president'];
                        if (in_array($asso['user_role']['type'], $acceptedRoles)) {
                            $adminStatus = MemberRole::Administrator;
                        }
                        break;
                    }
                }

                // Create a new user with the retrieved data
                $user = new User();
                $user->uuid = $userData->toArray()["uuid"];
                $user->email = $userData->toArray()["email"];
                $user->role = $adminStatus;
                if(User::where('uuid', $user->uuid)->count() > 0){
                    $user->update();
                }
                else{
                    if ($adminStatus != MemberRole::None) {
                        $user->save();
                    }
                }
                session(['user' => $user]);

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

    /**
     * Logs out the user from Ocktopus.
     *
     * @param  Request $request
     * @return mixed
     */
    public function logout(Request $request): mixed
    {
        $cookie = cookie(config('app.token_name'), null, -1);
        $request->session()->forget('user');
        return redirect('https://auth.assos.utc.fr/logout')->withCookie($cookie);
    }
}
