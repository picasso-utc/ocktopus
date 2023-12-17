<?php

namespace App\Http\Controllers;

use App\Models\GoodiesWinner;
use App\Services\ApiPayutcClient;
use App\Services\PayutSimdeProxyClient;
use Illuminate\Support\Facades\Http;

class GoodiesController extends Controller
{
    private ApiPayutcClient $payutc_client;
    private PayutSimdeProxyClient $proxy_client;

    public function __construct(ApiPayutcClient $payutc_client, PayutSimdeProxyClient $proxy_client)
    {
        $this->payutc_client = $payutc_client;
        $this->proxy_client = $proxy_client;
    }


    public function getWinner()
    {
        $response = $this->payutc_client->makePayutcRequest('GET', 'transactions', [
            'created__gt' => "2023-11-15T07:15:00.000000Z",
            'created__lt' => "2023-11-15T16:10:00.000000Z",
        ]);
        $responseData = $response->getContent();
        $jsonData = json_decode($responseData, true);
        $length = count($jsonData);
        $winners = [];
        while (count($winners) < 20) {
            $randomIndex = rand(0, $length - 1);
            $walletId = $jsonData[$randomIndex]['rows'][0]['payments'][0]['wallet_id'];
            $user = $this->proxy_client->makePayutcProxyRequest('POST', 'api/users/fromWallets', $walletId);
            dd($user);
            #$users = User::all()->where("role",MemberRole::Member)
            #$users = User::all()->where("role",MemberRole::Administrator)
            #if (!in_array($user, $winners)) {
            #    $winners[] = $walletId;
            #}
        }
        #dd($winners);
    }
}
