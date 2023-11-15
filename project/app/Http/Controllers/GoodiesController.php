<?php

namespace App\Http\Controllers;

use App\Models\GoodiesWinner;
use App\Http\Controllers\ApiPayutcController;

class GoodiesController extends Controller
{
    private ApiPayutcController $client;

    public function __construct(ApiPayutcController $client)
    {
        $this->client = $client;
    }

    public function getWinner()
    {
        $response = $this->client->makePayutcRequest('POST', 'GESSALES/getSales');
        dd($response);
    }
}

