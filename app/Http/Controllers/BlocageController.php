<?php

namespace App\Http\Controllers;

use App\Models\Blocages;
use App\Services\GingerClient;
use Illuminate\Http\Request;

class BlocageController extends Controller
{
    public function getBlocages(Request $request): mixed
    {
        Blocages::where('fin', '<', now())->delete();

        $gingerClient = new GingerClient;
        $blocages = Blocages::all()->pluck('cas')->map(function ($blockedUser) use ($gingerClient) {
            try {
                return $gingerClient->getUserInfo($blockedUser)['data']['badge_uid'];
            } catch (\Exception $e) {
                return null;
            }
        })->filter(fn($blockedUser) => $blockedUser != null);
        return $blocages;
    }
}
