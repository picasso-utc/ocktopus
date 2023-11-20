<?php

namespace App\Http\Controllers;

use App\Models\GoodiesWinner;
use App\Http\Controllers\ApiPayutcController;
use Illuminate\Support\Facades\Http;

class GoodiesController extends Controller
{
    private ApiPayutcController $client;

    public function __construct(ApiPayutcController $client)
    {
        $this->client = $client;
    }

    public function getWinner()
    {
        $response = $this->client->makePayutcRequest('GET', 'transactions', [
            'created__gt' => "2023-11-15T07:15:00.000000Z",
            'created__lt' => "2023-11-15T16:10:00.000000Z",
        ]);
        dd($response);
    }
}

