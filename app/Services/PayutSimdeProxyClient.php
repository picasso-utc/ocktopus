<?php

namespace App\Services;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class PayutSimdeProxyClient
{
    public function __construct()
    {
        $this->client = new Client();
    }

    public function makePayutcProxyRequest($method, $endpoint, $wallets)
    {
        $headers = [
          'X-Return-Structure' => 'array',
          'X-API-KEY' => '944E5613148CF65E1AD62DB2B5D47',
          'Content-Type' => 'application/json'
        ];
        $body = '{ "wallets": [' . $wallets .']}';
        $request = $this->client->request('POST', 'https://payutc.assos.utc.fr/api/users/fromWallets', $headers, $body);
        $res = $this->$client->sendAsync($request)->wait();
        return $res->getBody();
    }
}
