<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Client\Provider\GenericProvider;
use LogicException;
use UnexpectedValueException;

// Auth controller pour l'application mobile
class AuthController extends Controller
{
    public GenericProvider $provider;

    public function __construct()
    {
        $this->provider = new GenericProvider([
            'clientId' => config('services.oauth.client_id'),
            'clientSecret' => config('services.oauth.client_secret'),
            'redirectUri' => config('services.oauth.redirect_uri'), // valeur par défaut
            'urlAuthorize' => config('services.oauth.authorize_url'),
            'urlAccessToken' => config('services.oauth.access_token_url'),
            'urlResourceOwnerDetails' => config('services.oauth.owner_details_url'),
            'scopes' => config('services.oauth.scopes'),
        ]);
    }


    /**
        * Handle the login of a user via OAuth2 (generate a session token and redirect to the SiMDE OAuth)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function login(Request $request)
    {
        // mémoriser la cible post-auth
        // - en prod: flow normal
        // - en local RN: on passe ?target=mobile ou ?return_to=exp://... etc.
        if ($request->has('target')) {
            $request->session()->put('post_auth_target', $request->get('target'));
        }
        if ($request->has('return_to')) {
            $request->session()->put('return_to', $request->get('return_to'));
        }

        if (config('auth.app_no_login', false)) {
            $user_id = env('USER_ID');
            $user = User::find($user_id);

            if (!$user) {
                return response()->json([
                    'message' => 'Il faut au moins créer le user '.$user_id.' dans la base de données'
                ], 400);
            }

            try {
                $accessTokenPayload = [
                    'key' => $user_id,
                    'exp' => now()->addMinutes(60)->timestamp,
                ];
                $privateKey = config('services.crypt.private');
                $accessToken = JWT::encode($accessTokenPayload, $privateKey, 'RS256');

                $refreshTokenPayload = [
                    'key' => $user_id,
                    'exp' => now()->addDays(30)->timestamp,
                ];
                $refreshToken = JWT::encode($refreshTokenPayload, $privateKey, 'RS256');

                return redirect()->to('/mobile/api-connected?'.http_build_query([
                    'access_token'  => $accessToken,
                    'refresh_token' => $refreshToken,
                ]));
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Login error : '.$e->getMessage()
                ], 400);
            }
        }

        $state = bin2hex(random_bytes(16));
        $request->session()->put('oauth2state', $state);

        // Marquer que c'est un flow mobile pour que Connexion::auth() puisse router correctement
        $target = $request->get('target', 'mobile'); // /mobile/auth/login => mobile par défaut
        if ($target === 'mobile') {
            $request->session()->put('mobile_auth_flow', true);
        }

        // Utiliser la même redirectUri que le backoffice (celle enregistrée dans le client OAuth)
        $redirectUri = config('services.oauth.redirect_uri');

        // recrée le provider avec la redirectUri du backoffice
        $this->provider = new GenericProvider([
            'clientId' => config('services.oauth.client_id'),
            'clientSecret' => config('services.oauth.client_secret'),
            'redirectUri' => $redirectUri,
            'urlAuthorize' => config('services.oauth.authorize_url'),
            'urlAccessToken' => config('services.oauth.access_token_url'),
            'urlResourceOwnerDetails' => config('services.oauth.owner_details_url'),
            'scopes' => config('services.oauth.scopes'),
        ]);


        $authorizationUrl = $this->provider->getAuthorizationUrl([
            'state' => $state,
        ]);

        return redirect($authorizationUrl);
    }


    /**
     * Handle the callback of the SiMDE OAuth
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function callback(Request $request)
    {
        $storedState = $request->session()->pull('oauth2state');

        if (!$request->has('state') || $request->get('state') !== $storedState) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid state',
            ], 400);
        }

        if (!$request->has('code')) {
            return response()->json([
                'success' => false,
                'message' => 'No authorization code',
            ], 400);
        }

        try {
            // === OAuth access token (SiMDE) ===
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $request->get('code'),
            ]);

            $resourceOwner = $this->provider->getResourceOwner($accessToken);
            $userDetails = $resourceOwner->toArray();

            if (($userDetails['deleted_at'] ?? null) !== null || ($userDetails['active'] ?? 0) != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Compte supprimé ou désactivé',
                ], 401);
            }

            $email = $userDetails['email'] ?? null;
            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email manquant dans les infos OAuth',
                ], 400);
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur introuvable',
                ], 401);
            }

            // alumni/exte (si provider != cas)
            if (($userDetails['provider'] ?? null) !== 'cas') {
                $user->update(['alumniOrExte' => true]);
            }

            // === JWT mobile ===
            $accessTokenPayload = [
                'key' => $user->id,
                'exp' => now()->addMinutes(60)->timestamp,
            ];
            $privateKey = config('services.crypt.private');
            $accessToken = JWT::encode($accessTokenPayload, $privateKey, 'RS256');

            $refreshTokenPayload = [
                'key' => $user->id,
                'exp' => now()->addDays(30)->timestamp,
            ];
            $refreshToken = JWT::encode($refreshTokenPayload, $privateKey, 'RS256');

            $target = $request->session()->pull('post_auth_target', 'mobile');

            // retour WebView RN : URL que l'app intercepte
            if ($target === 'mobile') {
                return redirect()->to('/mobile/api-connected?'.http_build_query([
                    'access_token'  => $accessToken,
                    'refresh_token' => $refreshToken,
                ]));
            }

            // fallback web (si besoin)
            return redirect()->to('https://pic.assos.utc.fr');

        } catch (\Exception $e) {
            Log::error('Erreur callback OAuth mobile: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Callback error : '.$e->getMessage(),
            ], 400);
        }
    }


    /**
     * Refresh the access token of the user from his refresh token.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function refresh(Request $request)
    {
        try {
            $publicKey = config('services.crypt.public');
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['message' => "Refresh JWT absent pour l'authentification",'JWT_ERROR' => true], 400);
            }
            try {
                $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
            } catch (ExpiredException) {
                return response()->json(['message' => 'Refresh JWT expiré','JWT_ERROR' => true], 401);
            } catch (SignatureInvalidException) {
                return response()->json(['message' => 'Signature invalide pour le refresh JWT envoyé','JWT_ERROR' => true], 401);
            } catch (LogicException $e) {
                return response()->json(['message' => 'Erreur dans la configuration ou les clés du JWT de refresh', 'JWT_ERROR' => true], 400);
            } catch (UnexpectedValueException $e) {
                return response()->json(['message' => 'Le refresh JWT est mal formé ou contient des données invalides', 'JWT_ERROR' => true], 400);
            }
            $id = $decoded->key;
            $user = User::find($id);
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non trouvé pour le refresh token fourni', 'JWT_ERROR' => true], 404);
            }

            $accessTokenPayload = [
                'key' => $id,
                'exp' => now()->addMinutes(60)->timestamp,
            ];
            $privateKey = config('services.crypt.private');
            $accessToken = JWT::encode($accessTokenPayload, $privateKey, 'RS256');

            return response()->json(['access_token' => $accessToken]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du refresh: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors du refresh: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Get the user data from a token.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getUserData(Request $request)
    {
        $token = $request->bearerToken();

        try {
            $publicKey = config('services.crypt.public');
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

            $id = $decoded->key;

            $user = User::where('id', $id)->first();
            if (!$user) {
                return response()->json(['success' => 'false', 'message' => 'Utilisateur non trouvé'], 404);
            }

            return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => mailToName($user->email),
                'role' => $user->role,
                'admin' => $user->role->isAdministrator(),
                'member' => $user->role->isMember()
            ]
        ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des users infos: ' . $e->getMessage());
            return response()->json(['success' => 'false', 'message' => 'Erreur lors de la récupération des users infos : '.$e], 401);
        }
    }

    public function logout()
    {
        $cookie = cookie('auth_session', null, -1);
        return redirect('https://auth.assos.utc.fr/logout')->withCookie($cookie);
    }
}
