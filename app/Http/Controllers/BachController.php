<?php

namespace App\Http\Controllers;

use App\Services\GingerClient;
use App\Services\NemoPayClient;
use GuzzleHttp\Cookie\SetCookie;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class BachController extends Controller
{
    private GingerClient $gingerClient;
    private NemoPayClient $nemoPayClient;

    public function __construct(GingerClient $gingerClient, NemoPayClient $nemoPayClient)
    {
        $this->gingerClient = $gingerClient;
        $this->nemoPayClient = $nemoPayClient;
    }

    public function loginBadge(Request $request): mixed{
        $badge_uid = $request->input('badge_uid');
        $pin = $request->input('pin');
        $data = ['badge_id'=>$badge_uid, 'pin'=>$pin];
        try {
            return response($this->nemoPayClient->makeNemoPayRequest('POST', 'services/POSS3/loginBadge2', $data),200);
        }catch(GuzzleException $exception){
            return response(['title'=>"Problème avec le PIN fournis ou avec les permissions!",'message'=> "Vous avez soit remplit le mauvais PIN soit vous n'avez pas les droit de vous connecter"],400);
        }
    }

    public function loginCas(Request $request): mixed{
        $cas = $request->input('cas');
        if(empty($cas)){
            return response(['title' => "Vous avez pas fournis de CAS!","message"=> "Veuillez rentrer un CAS"], 400);
        }
        $userInfo = $this->gingerClient->getUserInfo($cas);
        if($userInfo['status'] == 500){
            return response(['title' => "Problème avec le CAS fournis!","message"=> "Ginger n'as trouver aucun compte avec ce CAS"], 400);
        }
        $request->merge(['badge_uid'=>$userInfo['data']['badge_uid']]);
        return $this->loginBadge($request);
    }
}
