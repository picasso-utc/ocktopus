<?php

namespace App\Http\Controllers;

use App\Services\PayUtcClient;
use Illuminate\Support\Facades\Http;

class TodayConsumptionController extends Controller
{
    private PayUtcClient $client;

    public function __construct(PayUtcClient $client)
    {
        $this->client = $client;
    }


    public function getTodayConsumption($productName)
    {
        $accumulatedData = [];
        $dateEnd = date('Y-m-d\TH:i:s.u\Z');
        $dateStart = date('Y-m-d\TH:i:s.u\Z', strtotime('today'));
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


    public function getSales($productNames)
    {uri:
        $productNames = explode(';', $productNames);
        // Initialisation des variables
        $accumulatedData = [];
        $dateEnd = date('Y-m-d\TH:i:s.u\Z');  // Date actuelle
        $dateStart = date('Y-m-d\TH:i:s.u\Z', strtotime('today'));  // Date du début de la journée

        // Récupération des ventes d'aujourd'hui
        $response = $this->client->makePayutcRequest('GET', 'transactions', [
            'created__gt' => $dateStart,
            'created__lt' => $dateEnd,
        ]);

        // Récupération de la réponse de Weezevent
        $responseData = $response->getContent();
        $jsonData = json_decode($responseData, true);
        $length = count($jsonData);
        $dateStart = $jsonData[$length-1]['created'];

        while ($length > 499) {  // Si toutes les ventes n'ont pas pu être récupérée
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

        // Traitement de la liste de ventes
        $productSales = array_fill_keys($productNames, 0);
        $length = count($accumulatedData);

        for ($i = 0; $i < $length; $i++) {
            for ($j = 0; $j < count($accumulatedData[$i]["rows"]); $j++){
                if (in_array($accumulatedData[$i]["rows"][$j]["item_name"], $productNames)){
                    $productName = $accumulatedData[$i]["rows"][$j]["item_name"];
                    $productSales[$productName] += $accumulatedData[$i]["rows"][$j]["payments"][0]["quantity"];
                }
            }
        }
        return $productSales;
    }
}
