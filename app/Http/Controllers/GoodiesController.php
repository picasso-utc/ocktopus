<?php

namespace App\Http\Controllers;

use App\Models\GoodiesWinner;
use App\Services\PayUtcClient;
use Illuminate\Support\Facades\Http;

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
        $response = $this->client->makePayutcRequest('GET', 'transactions', [
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

            if (!in_array($walletId, $winners)) {
                $winners[] = $walletId;
            }
        }
        dd($winners);
    }
}
