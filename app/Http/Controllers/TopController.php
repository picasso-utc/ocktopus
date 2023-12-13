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

    /**
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTop()
    {
        $dateString = "2023-11-15T07:15:00.000000Z";
        $date = new DateTime($dateString);
        $dateStart = new DateTime($dateString);

        $month = $date->format('m');
        $year = $date->format('Y');

        if ($month >= 1 && $month <= 2) {
            $year--;
        }

        if ($month >= 3 && $month <= 8) {
            $dateStart->setDate($year, 3, 1);
        } else {
            $dateStart->setDate($year, 9, 1);
        }
        $dateStart->setTime(0, 0, 0);
        $formattedDate = $dateStart->format('Y-m-d\TH:i:s.u\Z');
        $response = $this->client->makePayutcRequest('GET', 'transactions', [
            'created__gt' => $formattedDate,
            'created__lt' => $dateString,
        ]);
        $responseData = $response->getContent();
        $jsonData = json_decode($responseData, true);
        $length = count($jsonData);
        $id = 3;
        $dictionary = array();
        for ($i = 0; $i < $length; $i++) {
            for ($j = 0; $j < count($jsonData[$i]["rows"]); $j++){
                if ($jsonData[$i]["rows"][$j]["item_id"] == $id){
                    $walletId = $jsonData[$i]["rows"][0]["payments"][0]["wallet_id"];
                    $value = $jsonData[$i]["rows"][0]["payments"][0]["quantity"];

                    if (array_key_exists($walletId, $dictionary)){
                        $dictionary[$walletId] += $value;
                    }
                    $dictionary[$walletId] = $value;
                }
            }
        }
        dd($dictionary);
    }
}
