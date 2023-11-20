<?php

namespace App\Http\Middleware;

use App\Enums\MemberRole;
use App\Models\User;
use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use League\OAuth2\Client\Token\AccessToken;
use UnexpectedValueException;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->cookie(config('app.token_name'));
        if($token==null){
            $cookie_route=cookie('route',$request->route()->getName(),10);
            return redirect()->route('auth_route')->withCookie($cookie_route);
        }
        try{
            $public_key_path=storage_path('app/keys/public.key');
            $public_key=openssl_get_publickey('file://' . $public_key_path);
            $decoded_uuid = JWT::decode($token,new Key($public_key,'RS256'))->sub;
        }catch(ExpiredException){
            return response()->json(['message'=>'Json Web Token Expired','JWT_ERROR'=>true],401);
        }catch(SignatureInvalidException){
            return response()->json(['message'=>'Invalid Signature In Sent Json Web Token','JWT_ERROR'=>true],401);
        }catch (LogicException) {
            // errors having to do with environmental setup or malformed JWT Keys
            return response()->json(['message'=>'Error having to do with environmental setup or malformed JWT Keys','JWT_ERROR'=>true],401);
        } catch (UnexpectedValueException) {
            return response()->json(['message'=>'Error having to do with JWT signature and claims','JWT_ERROR'=>true],401);
        }

        $response = Http::withToken($token)->get('https://auth.assos.utc.fr/api/user/associations/current');
        if($response->failed()){
            return response()->json(['message'=>'Error while getting user infos','JWT_ERROR'=>true],401);
        }
        $userAssos=$response->json();

        $adminStatus = MemberRole::None;
        foreach ($userAssos as $asso) {
            if ($asso['login'] == 'picasso') {
                $adminStatus = MemberRole::Member;
                if ($asso['user_role']['type'] == 'developer') {
                    $adminStatus = MemberRole::Administrator;
                }
                break;
            }
        }

        if ($adminStatus != MemberRole::None) {
            $response = Http::withToken($token)->get('https://auth.assos.utc.fr/api/user/ginger');
            if ($response->failed()) {
                return response()->json(['message' => 'Error while getting user infos', 'JWT_ERROR' => true], 401);
            }
            $userInfos = $response->json();

            $user = User::firstOrCreate(
                [
                    'uuid' => $decoded_uuid,
                    'cas' => $userInfos['login'],
                    'email' => $userInfos['mail'],
                    'role' => $adminStatus,
                ]
            );
        }

        return $next($request);
    }
}
