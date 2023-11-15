<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ApiPayutcController extends Controller
{
    private $url = "https://api.nemopay.net/services/";
    private Client $client;
    private string $apiKey = "44682eb98b373105b99511d3ddd0034f";
    private string $systemId = "80405";

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->url,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'query' => [
                'system_id' => $this->systemId,
                'app_key' => $this->apiKey
            ],
        ]);
    }

    public function makePayutcRequest($method, $endpoint)
    {
        $apiUrl = $this->client->getConfig('base_url') . $endpoint;
        $response = $this->client->request($method, $apiUrl, [
                    'query' => [
                        'sessionid' => $this->getSession(),
                        'system_id' => $this->systemId,
                        'app_key' => $this->apiKey
                    ],

                ]);
        $data = json_decode($response->getBody(), true);
        return response()->json($data);
    }

    private function getSession(): string
    {
        $sessionTime = 30 * 60;
        return Cache::remember('payutc.session', $sessionTime, function () {
            $res = $this->client->post('WEBSALE/loginApp', [
                'body' => json_encode([
                    'key' => $this->apiKey,
                ]),
            ]);
            if ($res->getStatusCode() != 200) {
                throw new \Exception('Error while getting session from payutc');
            }
            $body = json_decode($res->getBody()->getContents(), true);
            return $body['sessionid'];
        });
    }
}
