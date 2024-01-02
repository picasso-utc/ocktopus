<?php

namespace App\Http\Controllers;

use App\Models\GoodiesWinner;
use App\Services\PayUtcClient;
use Illuminate\Support\Facades\Http;
use DateTime;

class GoodiesController extends Controller
{
    private PayUtcClient $client;

    /**
     * @param PayUtcClient $client
     */
    public function __construct(PayUtcClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWinner()
    {
        $accumulatedData = [];
        $dateStart = (new DateTime("2023-12-10"))->format('Y-m-d\TH:i:s.u\Z');
        $dateEnd = (new DateTime("2023-12-16"))->format('Y-m-d\TH:i:s.u\Z');
        $response = $this->client->makePayutcRequest('GET', 'transactions', [
            'created__gt' => $dateStart,
            'created__lt' => $dateEnd,
        ]);
        $jsonData = json_decode($response->getContent(), true);
        $length = count($jsonData);
        $dateStart = $jsonData[$length-1]['created'];
        while ($length > 499) {
            $accumulatedData = array_merge($accumulatedData, $jsonData);
            $response = $this->client->makePayutcRequest('GET', 'transactions', [
                'created__gt' => $dateStart,
                'created__lt' => $dateEnd,
            ]);
            $jsonData = json_decode($response->getContent(), true);
            $length = count($jsonData);
            $dateStart = $jsonData[$length-1]['created'];
        }
        $accumulatedData = array_merge($accumulatedData, $jsonData);
        $length = count($accumulatedData);
        $winners = [];
        while (count($winners) < 20) {
            $randomIndex = rand(0, $length - 1);
            $walletId = $accumulatedData[$randomIndex]['rows'][0]['payments'][0]['wallet_id'];
            if (!in_array($walletId, $winners)) {
                $winners[] = $walletId;
            }
        }
        $user = Http::withHeaders([
          'X-Return-Structure' => 'array',
          'Content-Type' => 'application/json',
          'X-API-KEY' => env('PROXY_KEY')
        ])->post(env('PROXY_URL'), [
                  'wallets' => $winners,
            ]);
        for ($i = 0; $i < 20; $i++) {
            $winnersname[] = $user->json()[$i]['firstname'] . ' ' . $user->json()[$i]['lastname'];
        }
        dd($winnersname);
    }
}
