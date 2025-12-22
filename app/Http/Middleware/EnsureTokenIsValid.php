<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use UnexpectedValueException;

class EnsureTokenIsValid
{
    public function handle(Request $request, Closure $next): Response
    {
        $publicKey = config('services.crypt.public');
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => "JWT absent pour l'authentification", 'JWT_ERROR' => true], 401);
        }

        try {
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
        } catch (ExpiredException) {
            return response()->json(['message' => 'JWT expiré', 'JWT_ERROR' => true], 401);
        } catch (SignatureInvalidException) {
            return response()->json(['message' => 'Signature invalide pour le JWT envoyé', 'JWT_ERROR' => true], 401);
        } catch (LogicException) {
            return response()->json(['message' => 'Erreur dans la configuration ou les clés JWT', 'JWT_ERROR' => true], 401);
        } catch (UnexpectedValueException) {
            return response()->json(['message' => 'Le JWT est mal formé ou contient des données invalides', 'JWT_ERROR' => true], 401);
        }

        $id = $decoded->key ?? null;
        if (!$id) {
            return response()->json(['message' => 'JWT invalide: champ key manquant', 'JWT_ERROR' => true], 401);
        }

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé pour le token fourni', 'JWT_ERROR' => true], 404);
        }

        $request->merge(['user' => $user->toArray()]);

        return $next($request);
    }
}
