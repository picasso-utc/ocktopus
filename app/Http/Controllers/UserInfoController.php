<?php

namespace App\Http\Controllers;

use App\Services\GingerClient;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class UserInfoController extends Controller
{
    private $gingerClient;

    public function __construct(GingerClient $gingerClient)
    {
        $this->gingerClient = $gingerClient;
    }

    public function getUserInfo(Request $request): mixed
    {
        $cas = $request->input('cas');

        $userInfo = $this->gingerClient->getUserInfo($cas);

        return view('userinfo', ['userInfo' => $userInfo]);
    }
}
