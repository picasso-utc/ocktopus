<?php

namespace App\Http\Controllers;

use App\Models\Room;
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

class AuthController extends Controller
{
    public GenericProvider $provider;

    public function __construct()
    {
        $this->provider = new GenericProvider([
            'clientId'                => config('services.oauth.client_id'),
            'clientSecret'            => config('services.oauth.client_secret'),
            'redirectUri'             => config('services.oauth.redirect_uri'), // doit pointer vers /mobile/auth/callback
            'urlAuthorize'            => config('services.oauth.authorize_url'),
            'urlAccessToken'          => config('services.oauth.access_token_url'),
            'urlResourceOwnerDetails' => config('services.oauth.owner_details_url'),
            'scopes'                  => config('services.oauth.scopes'),
        ]);
    }

    /**
     * Démarre le flow OAuth (mobile)
     */
    public function login(Request $request)
    {
        // mode dev optionnel (bypass)
        if (config('auth.app_no_login', false)) {
            $userId = env('USER_ID');
            $user = User::find($userId);

            if (!$user) {
                return response()->json([
                    'message' => 'Il faut au moins créer le user '.$userId.' dans la base de données',
                ], 400);
            }

            $privateKey = config('services.crypt.private');

            $jwtAccess = JWT::encode([
                'key' => $userId,
                'exp' => now()->addMinutes(60)->timestamp,
            ], $privateKey, 'RS256');

            $jwtRefresh = JWT::encode([
                'key' => $userId,
                'exp' => now()->addDays(30)->timestamp,
            ], $privateKey, 'RS256');

            return redirect()->route('mobile.api-connected', [
                'access_token'  => $jwtAccess,
                'refresh_token' => $jwtRefresh,
            ]);
        }

        // state anti-CSRF
        $state = bin2hex(random_bytes(16));
        $request->session()->put('oauth2state', $state);

        $authorizationUrl = $this->provider->getAuthorizationUrl([
            'state' => $state,
        ]);

        // construit l’URL authorize
        $authorizationUrl = $this->provider->getAuthorizationUrl([
            'state' => $state,
        ]);

        return redirect()->away($authorizationUrl);
    }

    /**
     * Callback OAuth (mobile)
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
            return redirect()->route('mobile.api-not-connected', [
                'message' => 'No authorization code',
            ]);
        }

        try {
            $oauthAccessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $request->get('code'),
            ]);

            $resourceOwner = $this->provider->getResourceOwner($oauthAccessToken);
            $userDetails = $resourceOwner->toArray();

            if (($userDetails['deleted_at'] ?? null) !== null || ($userDetails['active'] ?? 0) != 1) {
                return redirect()->route('mobile.api-not-connected', [
                    'message' => 'Compte supprimé ou désactivé',
                ]);
            }

            $email = $userDetails['email'] ?? null;
            if (!$email) {
                return redirect()->route('mobile.api-not-connected', [
                    'message' => 'Email manquant dans les infos OAuth',
                ]);
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                return redirect()->route('mobile.api-not-connected', [
                    'message' => 'Utilisateur introuvable',
                ]);
            }

            // alumni/exte (si provider != cas)
            if (($userDetails['provider'] ?? null) !== 'cas') {
                $user->update(['alumniOrExte' => true]);
            }

            // génère JWT mobile
            $privateKey = config('services.crypt.private');

            $jwtAccess = JWT::encode([
                'key' => $user->id,
                'exp' => now()->addMinutes(60)->timestamp,
            ], $privateKey, 'RS256');

            $jwtRefresh = JWT::encode([
                'key' => $user->id,
                'exp' => now()->addDays(30)->timestamp,
            ], $privateKey, 'RS256');

            return redirect()->route('mobile.api-connected', [
                'access_token'  => $jwtAccess,
                'refresh_token' => $jwtRefresh,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur callback OAuth mobile: '.$e->getMessage());
            return redirect()->route('mobile.api-not-connected', [
                'message' => 'Callback error : '.$e->getMessage(),
            ]);
        }
    }

    /**
     * Refresh access token à partir du refresh token (Bearer)
     */
    public function refresh(Request $request)
    {
        try {
            $publicKey = config('services.crypt.public');
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'message' => "Refresh JWT absent pour l'authentification",
                    'JWT_ERROR' => true,
                ], 400);
            }

            try {
                $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
            } catch (ExpiredException) {
                return response()->json(['message' => 'Refresh JWT expiré', 'JWT_ERROR' => true], 401);
            } catch (SignatureInvalidException) {
                return response()->json(['message' => 'Signature invalide', 'JWT_ERROR' => true], 401);
            } catch (LogicException) {
                return response()->json(['message' => 'Erreur config/clefs JWT', 'JWT_ERROR' => true], 400);
            } catch (UnexpectedValueException) {
                return response()->json(['message' => 'Refresh JWT mal formé', 'JWT_ERROR' => true], 400);
            }

            $id = $decoded->key;
            $user = User::find($id);

            if (!$user) {
                return response()->json(['message' => 'Utilisateur non trouvé', 'JWT_ERROR' => true], 404);
            }

            $privateKey = config('services.crypt.private');
            $jwtAccess = JWT::encode([
                'key' => $id,
                'exp' => now()->addMinutes(60)->timestamp,
            ], $privateKey, 'RS256');

            return response()->json(['access_token' => $jwtAccess]);

        } catch (\Exception $e) {
            Log::error('Erreur refresh mobile: '.$e->getMessage());
            return response()->json(['message' => 'Erreur refresh: '.$e->getMessage()], 400);
        }
    }

    /**
     * Récupère les infos user via access token (Bearer)
     */
    public function getUserData(Request $request)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['success' => false, 'message' => 'Access token manquant'], 401);
            }

            $publicKey = config('services.crypt.public');
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

            $id = $decoded->key;
            $user = User::find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Utilisateur non trouvé'], 404);
            }

            // chez toi: roomID
            $room = Room::where('id', $user->roomID)->first();

            return response()->json([
                'success' => true,
                'id' => $user->id,
                'name' => $user->firstName,
                'lastName' => $user->lastName,
                'room' => $room?->roomNumber,
                'roomName' => $room?->name,
                'admin' => $user->admin,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getUserData mobile: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur récupération infos user',
            ], 401);
        }
    }

    public function logout()
    {
        $cookie = cookie('auth_session', null, -1);
        return redirect('https://auth.assos.utc.fr/logout')->withCookie($cookie);
    }
}
