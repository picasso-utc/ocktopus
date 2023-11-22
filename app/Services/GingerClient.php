<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GingerClient
{
    private array $config;

    public function __construct()
    {
        $this->config = [
            'url' => env('GINGER_URI'),
            'key' => env('GINGER_KEY'),
        ];
    }

    /**
     * Get user information based on a username.
     *
     * @param string $username
     * @return array
     */
    public function getUserInfo(string $username): array
    {
        return $this->apiCall('GET', '/' . $username);
    }

    /**
     * Get badge information based on a badge ID.
     *
     * @param string $badgeId
     * @return array
     */
    public function getBadgeInfo(string $badgeId): array
    {
        return $this->apiCall('GET', '/badge/' . $badgeId);
    }

    /**
     * Perform API call to Ginger/v1.
     *
     * @param string $method
     * @param string $path
     * @param array|null $data
     * @param array|null $parameters
     * @return array
     */
    private function apiCall(string $method, string $path, array $data = null, array $parameters = null) : array
    {
        $uri = $this->config['url'] . $path;
        $key = $this->config['key'];

        // Add the API key as a parameter
        $parameters['key'] = $key;

        $response = Http::{$method}($uri, $parameters);

        return $this->buildResponse($response);
    }


    /**
     * Build a response for the API call.
     *
     * @param Response $apiResponse
     * @return array
     */
    private function buildResponse(Response $apiResponse): array
    {
        return [
            'data' => $apiResponse->json(),
            'status' => $apiResponse->status(),
        ];
    }
}
