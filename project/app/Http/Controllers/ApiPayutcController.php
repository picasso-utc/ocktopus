<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ApiPayutcController extends Controller
{
    private Client $client;
    private string $apiKey = "44682eb98b373105b99511d3ddd0034f";
    private string $systemId = "80405";
    private $url = "https://api.weezevent.com/pay/v1/organizations/80405/";

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->url,
            'query' => [
                'system_id' => $this->systemId,
                'app_key' => "44682eb98b373105b99511d3ddd0034f",
            ],
        ]);
    }

    public function makePayutcRequest($method, $endpoint)
    {
        $apiUrl = $this->client->getConfig('base_url') . $endpoint;
        $response = $this->client->request($method, $apiUrl, [
                    'query' => [
                        'app_key' => "44682eb98b373105b99511d3ddd0034f",
                        'event' => 1,
                        'sessionid' => $this->getSession(),
                        'system_id' => '80405',
                       /* 'created__gt' => "2023-11-10T09:15:00.000000Z",
                        'created__lt' => "2023-11-13T09:15:00.000000Z",*/
                        'fundation_id' => 2,
                    ],
                    'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Session '.$this->getSession()
                    ],
                ]);
        $data = json_decode($response->getBody(), true);
        return response()->json($data);
    }

    public function getSession(): string
    {
        $sessionTime = 0;
        return Cache::remember('payutc.session2', $sessionTime, function () {
            $res = $this->client->request('POST', 'https://api.nemopay.net/services/WEBSALE/login2', [
                'body' => json_encode([
                    'password' => 'g2psZPvDsbADAm5',
                    'login' => 'service-account-pic@assos.utc.fr',
                ]),
                'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            ]);
            if ($res->getStatusCode() != 200) {
                throw new \Exception('Error while getting session from payutc');
            }
            $body = json_decode($res->getBody()->getContents(), true);
            return $body['sessionid'];
        });
    }
}
