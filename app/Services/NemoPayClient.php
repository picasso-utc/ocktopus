<?php

namespace App\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class NemoPayClient
{

    public function __construct()
    {
        // On crée une nouvelle instance de la classe Client de Guzzle.
        $this->client = new Client(
            [
                // L'URL de base pour les requêtes de l'API est récupérée à partir de la variable d'environnement API_URL.
                'base_uri' => config('app.nemopay_api_url'),

                // Les paramètres de requête communs à toutes les requêtes sont configurés ici.

                // Les en-têtes communs à toutes les requêtes sont configurés ici.
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]
        );
    }

    /**
     * @throws GuzzleException
     */
    public function makeNemoPayRequest($method, $endpoint, $data=[])
    {
        $params = [
            'query' => [
                'system_id' => config('app.payutc_system_id'),
                'app_key' => config('app.payutc_app_key'),
            ],
            'form_params'=>$data,
        ];
        $response = $this->client->request($method, $endpoint, $params);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            // Décodage de la réponse JSON
            return json_decode($response->getBody(), true);
        } else {
            // Gestion des erreurs en cas de réponse non réussie
            $errorMessage = 'Failed to retrieve data from Payutc API. Status Code: ' . $response->getStatusCode();
            return response()->json(['error' => $errorMessage], $response->getStatusCode());
        }
    }
}
