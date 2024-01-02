<?php

namespace App\Http\Controllers;

use App\Services\PayUtcClient;
use Illuminate\Support\Facades\Http;
use DateTime;

class TopController extends Controller
{
    private PayUtcClient $client;

    /**
     * @param PayUtcClient $client
     */
    public function __construct(PayUtcClient $client)
    {
        $this->client = $client;
    }

    public function getTop($productName)
    {
        $accumulatedData = [];
        $dateStart = (new DateTime("2023-12-17"))->format('Y-m-d\TH:i:s.u\Z');
        $dateEnd = (new DateTime())->format('Y-m-d\TH:i:s.u\Z');
        $response = $this->client->makePayutcRequest('GET', 'transactions', [
            'created__gt' => $dateStart,
            'created__lt' => $dateEnd,
        ]);
        $responseData = $response->getContent();
        $jsonData = json_decode($responseData, true);
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
        $dictionary = array();
        for ($i = 0; $i < $length; $i++) {
            for ($j = 0; $j < count($accumulatedData[$i]["rows"]); $j++){
                if ($accumulatedData[$i]["rows"][$j]["item_name"] == $productName){
                    $walletId = $accumulatedData[$i]["rows"][$j]["payments"][0]["wallet_id"];
                    if (!in_array($walletId, $winners)) {
                        $winners[] = $walletId;
                    }
                    $value = $accumulatedData[$i]["rows"][$j]["payments"][0]["quantity"];

                    if (array_key_exists($winners, $dictionary)){
                        $dictionary[$winners] += $value;
                    }
                    $dictionary[$winners] = $value;
                }
            }
        }
        dd($dictionary);
    }
}
