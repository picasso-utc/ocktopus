<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiPayutcController;
use Illuminate\Support\Facades\Http;

class GoodiesController extends Controller
{
    private ApiPayutcController $client;

    public function __construct(ApiPayutcController $client)
    {
        $this->client = $client;
    }

    public function createTop()
    {
        $dateString = "2023-11-15T07:15:00.000000Z";
        $date = new DateTime($dateString);

        $month = $date->format('m');
        $year = $date->format('y');

        if ($month >= 1 && $month <= 2) {
            $year--;
        }

        if ($month >= 3 && $month <= 8) {
            $dateStart->setDate($year, 3, 1)
        } else {
            $dateStart->setDate($year, 9, 1)
        }

        $response = $this->client->makePayutcRequest('GET', 'transactions', [
            'created__gt' => $dateStart,
            'created__lt' => $date,
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
