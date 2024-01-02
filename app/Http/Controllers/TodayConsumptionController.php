<?php

namespace App\Http\Controllers;

use App\Models\GoodiesWinner;
use App\Services\ApiPayutcClient;
use Illuminate\Support\Facades\Http;

class TodayConsumptionController extends Controller
{
    private ApiPayutcClient $client;

    public function __construct(ApiPayutcClient $client)
    {
        $this->client = $client;
    }


    public function getTodayConsumption($productName)
    {
        $accumulatedData = [];
        $dateStart = date('Y-m-d\TH:i:s.u\Z');
        $dateEnd = date('Y-m-d\TH:i:s.u\Z', strtotime('today'));
        $response = $this->client->makePayutcRequest('GET', 'transactions', [
            'created__gt' => $dateStart,
            'created__lt' => $dateEnd,
        ]);
        dd($dateStart);
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
        $value = 0;
        for ($i = 0; $i < $length; $i++) {
            for ($j = 0; $j < count($accumulatedData[$i]["rows"]); $j++){
                if ($accumulatedData[$i]["rows"][$j]["item_name"] == $productName){
                    $value2 = $accumulatedData[$i]["rows"][$j]["payments"][0]["quantity"];
                    $value += $value2;
                }
            }
        }
        dd($value);
    }
}
