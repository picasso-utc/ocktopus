<?php
// Auth CAS pour l'app mobile du pic uniquement

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use League\OAuth2\Client\Provider\GenericProvider;
use LogicException;
use UnexpectedValueException;

class AuthController extends Controller
{
    /**
     * Le fournisseur d'OAuth2.
     */
    public GenericProvider $provider;

    /**
     * Crée un nouveau contrôleur AuthController.
     */
    public function __construct()
    {
        // Initialisation du fournisseur OAuth2 avec les valeurs de configuration des variables d'environnement
        $this->provider = new GenericProvider([
            'clientId'                => config('services.oauth.client_id'),
            'clientSecret'            => config('services.oauth.client_secret'),
            'redirectUri'             => config('services.oauth.redirect_uri'),
            'urlAuthorize'            => config('services.oauth.authorize_url'),
            'urlAccessToken'          => config('services.oauth.access_token_url'),
            'urlResourceOwnerDetails' => config('services.oauth.owner_details_url'),
            'scopes'                  => config('services.oauth.scopes'),
        ]);
    }

    /**
     * Gère le login d'un utilisateur via OAuth2 (génère un token de session et renvoie vers l'OAuth du SiMDE)
     */
    public function login(Request $request)
    {
        //return response("MOBILE LOGIN HIT", 200);
        if (config('auth.app_no_login', false)) {
            $userId = env('USER_ID');
            $user = User::find($userId);
            if (!$user) {
                response()->json(['message' => 'Il faut au moins créer le user '.$userId.' dans la base de données'], 400);
            }
            try {
                $accessTokenPayload = [
                    'key' => $userId,
                    'exp' => now()->addMinutes(60)->timestamp,
                ];
                $privateKey = config('services.crypt.private');
                $accessToken = JWT::encode($accessTokenPayload, $privateKey, 'RS256');

                $refreshTokenPayload = [
                    'key' => $userId,
                    'exp' => now()->addDays(30)->timestamp,
                ];
                $refreshToken = JWT::encode($refreshTokenPayload, $privateKey, 'RS256');

                return redirect()->route('mobile.api-not-connected', [
                    'message' => 'Callback error : ' . $e->getMessage() // il faut que l'app mobile ouvre directement http://127.0.0.1:8000/mobile/auth/login et Back Office (on veut pas toucher Auth.php)
                ]);

            } catch (\Exception $e) {
                return response()->json(['message' => 'Login error :'. $e->getMessage()], 400);
            }
        }

        $state = bin2hex(random_bytes(16));   // génère un token de session aléatoire
        $request->session()->put('oauth2state', $state);

        $authorizationUrl = $this->provider->getAuthorizationUrl([    // Redirection vers l'URL d'autorisation de l'OAuth avec le token
            'state' => $state
        ]);

        return redirect($authorizationUrl);
    }

    /**
     * Gère le callback de l'OAuth du SiMDE
     */
    public function callback(Request $request)
    {
        $storedState = $request->session()->pull('oauth2state');

        if (!$request->has('state') || $request->get('state') !== $storedState) {   // Vérifie que le token de session est valide
            abort(400, 'Invalid state: '. $request->get('state') . ' VS ' . $storedState);
        }

        if (!$request->has('code')) {     // Vérifie que le code d'autorisation est présent
            abort(400, 'No authorization code');
        }
        try {
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $request->get('code'),
            ]);

            $resourceOwner = $this->provider->getResourceOwner($accessToken);
            $userDetails = $resourceOwner->toArray();

            if ($userDetails['deleted_at'] != null || $userDetails['active'] != 1) {
                abort(401, 'Compte supprimé ou désactivé');
            }

            $user = User::where('email', $userDetails['email'])->first();  // Si le user n'existe pas
            if (!$user) {
                abort(401, "Utilisateur introuvable");
            }

            if (($userDetails['provider'] ?? null) !== 'cas') {
                $user->update(['alumniOrExte' => true]);
            }

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

            return redirect()->route('api-connected', [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('api-not-connected', [
                'message' => 'Callback error : ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Refresh l'access token du user à partir de son refresh token.
     */
    public function refresh(Request $request)
    {
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
    }

    /**
     * Récupère les informations de l'utilisateur à partir d'un token.
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

            $room = Room::where('id', $user->roomID)->first();
            if (!$room) {
                return response()->json(['success' => 'false', 'message' => 'Chambre non trouvée'], 404);
            }

            return response()->json([
                'success' => true,
                'id' => $user->id,
                'name' => $user->firstName,
                'lastName' => $user->lastName,
                'room' => $room->roomNumber,
                'roomName' => $room->name ? $room->name : null,
                'admin' => $user->admin
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => 'false', 'message' => 'Erreur lors de la récupération des users infos : '.$e], 401);
        }
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout()
    {
        $cookie = cookie('auth_session', null, -1);
        return redirect('https://auth.assos.utc.fr/logout')->withCookie($cookie);
    }
}
