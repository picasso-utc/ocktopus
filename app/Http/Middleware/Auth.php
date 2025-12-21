<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;
use Illuminate\Support\Facades\Auth as LaravelAuth;


class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  Request                      $request
     * @param  Closure(Request): (Response) $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ne jamais appliquer l’auth backoffice aux routes mobile
        if ($request->is('mobile/*') || $request->is('api/mobile/*')) {
            return $next($request);
        }
        // ===== Bypass JWT en environnement local / testing =====
        /*
        if (app()->environment(['local', 'testing'])) {
            // Force connexion sur Dina (id = 63)
            $user = \App\Models\User::find(63);

            if ($user === null) {
                // Récupérer un utilisateur de test existant
                $user = User::first();

                if ($user) {
                    // Connecte l'utilisateur dans l'auth guard
                    LaravelAuth::loginUsingId($user->id);
                    session(['user' => $user]);
                } else {
                    // Si aucun user en base, créer un utilisateur factice minimal
                    $user = new User();
                    $user->id = 0;
                    $user->uuid = 'local-fake-uuid';
                    $user->name = 'Local Dev User';
                }
            }

            auth()->setUser($user);

            // Continue avec la requête
            $panel = Filament::getCurrentPanel();

            abort_if(
                $user instanceof FilamentUser ?
                    (! $user->canAccessPanel($panel)) :
                    false, // bypass en dev
                403,
            );

            return $next($request);
        }
        */
        if (app()->environment(['local', 'testing'])) {
            // Force connexion sur Dina (id = 63)
            $user = \App\Models\User::find(63);

            if ($user === null) {
                // Crée un utilisateur factice avec UUID de l'id choisi
                $user = new User();
                $user->id = 63;
                $user->uuid = '984e2a30-1fbc-11ec-bc5d-b32b8e873763';
                $user->name = 'Local Dev User';
                $user->email = 'dina.mouayed@etu.utc.fr';
            }

            // Auth Laravel
            LaravelAuth::loginUsingId($user->id);
            session(['user' => $user]);

            auth()->setUser($user);

            $panel = Filament::getCurrentPanel();
            abort_if(
                $user instanceof FilamentUser ?
                    (! $user->canAccessPanel($panel)) :
                    false,
                403,
            );

            return $next($request);
        }


        // ===== Environnement prod / autres =====
        // Récupérer le token depuis le cookie
        $token = $request->cookie(config('app.token_name'));

        if ($token == null) {
            $cookie_route = cookie('route', $request->route()->getName(), 10);
            return redirect()->route('auth_route')->withCookie($cookie_route);
        }

        try {
            // Lire le contenu brut de la clé publique (string PEM)
            $public_key_path = storage_path('app/keys/public.key');
            $public_key = file_get_contents($public_key_path);
            $decoded_uuid = JWT::decode($token, new Key($public_key, 'RS256'))->sub;

            if ($decoded_uuid == null) {
                return redirect()->route('auth_route')->withCookie(cookie('route', $request->route()->getName(), 10));
            }

            $user = session('user');
            if ($user == null || $user->uuid != $decoded_uuid) {
                return redirect()->route('auth_route')->withCookie(cookie('route', $request->route()->getName(), 10));
            }

            // Set the user as the authenticated user
            auth()->setUser($user);
        } catch (ExpiredException) {
            return response()->json(['message' => 'Json Web Token Expired', 'JWT_ERROR' => true], 401);
        } catch (SignatureInvalidException) {
            return response()->json(['message' => 'Invalid Signature In Sent Json Web Token', 'JWT_ERROR' => true], 401);
        } catch (LogicException) {
            return response()->json(['message' => 'Error having to do with environmental setup or malformed JWT Keys', 'JWT_ERROR' => true], 401);
        } catch (UnexpectedValueException) {
            return response()->json(['message' => 'Error having to do with JWT signature and claims', 'JWT_ERROR' => true], 401);
        }

        $panel = Filament::getCurrentPanel();

        abort_if(
            $user instanceof FilamentUser ?
                (! $user->canAccessPanel($panel)) :
                (config('app.env') !== 'local'),
            403,
        );

        // Continue with the request
        return $next($request);
    }
}
