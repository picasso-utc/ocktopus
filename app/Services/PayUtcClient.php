<?php

namespace App\Services;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class PayUtcClient
{
    public function __construct()
    {
        // On crée une nouvelle instance de la classe Client de Guzzle.
        $this->client = new Client(
            [
            // L'URL de base pour les requêtes de l'API est récupérée à partir de la variable d'environnement API_URL.
            'base_url' => config('app.payutc_api_url'),

            // Les paramètres de requête communs à toutes les requêtes sont configurés ici.
            'query' => [
                'system_id' => config('app.payutc_system_id'),
                'app_key' => config('app.payutc_app_key'),
                'fundation_id' => config('app.payutc_fundation_id'),
                'event_id' => 1,                      // L'ID de l'événement est fixé à 1 (c'est l'id du picasso).
                'status' => "V",                      // Le statut est fixé à "V" comme validate.
            ],

            // Les en-têtes communs à toutes les requêtes sont configurés ici.
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            ]
        );
    }

    /**
     * Fonction générale qui permet de faire une requête a l'API dont la doc est présente ici :
     * https://docapi.weezevent.com/openapi.html?weezpay
     *
     * @param  $method
     * @param  $endpoint
     * @param  $options
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function makePayutcRequest($method, $endpoint, $options = [])
    {
        try {
            // Obtention de l'ID de session
            $session_id = $this->getSession();

            // Initialisation des variables pour la pagination
            $currentPage = 0;
            $perPage = 500; // Nombre d'éléments par page (ajustez selon vos besoins)
            $allData = [];

            do {
                // Construction de l'URL complète de l'API avec le numéro de page actuel
                $apiUrl = $this->client->getConfig('base_url') . $endpoint;

                // Construction des paramètres de requête en fusionnant les options avec les valeurs par défaut
                $query = array_merge(
                    [
                    'sessionid' => $session_id,
                    ],
                    $options
                );

                // Configuration de la requête
                $requestConfig = [
                    'query' => $query,
                    'headers' => [
                        'Authorization' => 'Session ' . $session_id,
                    ],
                ];

                // Envoi de la requête à l'API
                $response = $this->client->request($method, $apiUrl, $requestConfig);

                // Vérification de la réussite de la réponse (code de statut 2xx) avant le décodage
                if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                    // Décodage de la réponse JSON
                    $data = json_decode($response->getBody(), true);

                    // Ajout des données de la page actuelle à l'ensemble des données
                    $allData = array_merge($allData, $data);

                    // Passage à la page suivante
                    $currentPage++;
                } else {
                    // Gestion des erreurs en cas de réponse non réussie
                    $errorMessage = 'Failed to retrieve data from Payutc API. Status Code: ' . $response->getStatusCode();
                    return response()->json(['error' => $errorMessage], $response->getStatusCode());
                }

                // Continue tant que le nombre d'éléments dans la réponse est égal au nombre attendu par page
            } while ($currentPage != 1);

            // Retour d'une réponse JSON avec l'ensemble des données
            return response()->json($allData);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Gestion des exceptions liées à la requête HTTP
            $errorMessage = 'HTTP Request Exception: ' . $e->getMessage();
            return response()->json(['error' => $errorMessage], 500);
        } catch (\Exception $e) {
            // Gestion des autres exceptions
            $errorMessage = 'An error occurred: ' . $e->getMessage();
            return response()->json(['error' => $errorMessage], 500);
        }
    }

    /**
     * Fonction qui requête à l'ancienne API l'id de session pour utiliser cet id dans la nouvelle api
     * documentation ici : https://apidoc.nemopay.net/Base_Service/#loginApp
     *
     * @return string
     */
    public function getSession(): string
    {
        // Durée de validité de la session en secondes (30 minutes)
        $sessionTime = 30 * 60;

        // Utilisation du cache Laravel pour stocker et récupérer la session
        return Cache::remember(
            'payutc.session',
            $sessionTime,
            function () {
                // Requête pour obtenir une session auprès de l'API Payutc
                $res = $this->client->request(
                    'POST',
                    'https://api.nemopay.net/services/WEBSALE/login2',
                    [
                    'body' => json_encode(
                        [
                        'password' => config('app.payutc_password'),
                        'login' => config('app.payutc_login'),
                        ]
                    ),
                    'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    ],
                    ]
                );

                // Vérification du code de statut de la réponse
                if ($res->getStatusCode() != 200) {
                    // En cas d'échec, une exception est levée
                    throw new \Exception('Erreur lors de l\'obtention de la session depuis Payutc');
                }

                // Décodage du corps de la réponse JSON
                $body = json_decode($res->getBody()->getContents(), true);

                // Retourne l'identifiant de session obtenu
                return $body['sessionid'];
            }
        );
    }
}
