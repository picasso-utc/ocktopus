<?php

namespace App\Filament\Admin\Resources\GoodiesResource\Pages;

use App\Filament\Admin\Resources\GoodiesResource;
use App\Models\Goodies;
use App\Services\PayUtcClient;
use DateTime;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ManageGoodies extends ManageRecords
{
    protected static string $resource = GoodiesResource::class;

    /**
     * @throws GuzzleException
     */
    protected function generateWinners(): void
    {
        $client = new PayUtcClient();

        $accumulatedData = [];
        $dateStart = date('Y-m-d\TH:i:s.u\Z', strtotime('-7 days'));
        $dateEnd = date('Y-m-d\TH:i:s.u\Z', strtotime('today'));
        $response = $client->makePayutcRequest(
            'GET',
            'transactions',
            [
            'created__gt' => $dateStart,
            'created__lt' => $dateEnd,
            ]
        );
        $jsonData = json_decode($response->getContent(), true);
        $length = count($jsonData);
        $dateStart = $jsonData[$length - 1]['created'];

        while ($length > 499) {
            $accumulatedData = array_merge($accumulatedData, $jsonData);
            $response = $client->makePayutcRequest(
                'GET',
                'transactions',
                [
                'created__gt' => $dateStart,
                'created__lt' => $dateEnd,
                ]
            );
            $jsonData = json_decode($response->getContent(), true);
            $length = count($jsonData);
            $dateStart = $jsonData[$length - 1]['created'];
        }

        $accumulatedData = array_merge($accumulatedData, $jsonData);
        $length = count($accumulatedData);

        $winners = [];
        while (count($winners) < 20) {
            $randomIndex = rand(0, $length - 1);
            $walletId = $accumulatedData[$randomIndex]['rows'][0]['payments'][0]['wallet_id'];
            if (!in_array($walletId, $winners)) {
                $winners[] = $walletId;
            }
        }

        $user = Http::withHeaders(
            [
            'X-Return-Structure' => 'array',
            'Content-Type' => 'application/json',
            'X-API-KEY' => config('app.proxy_key')
            ]
        )->post(
            config('app.proxy_url'),
            [
                'wallets' => $winners,
                ]
        );

        $winnersName = [];
        for ($i = 0; $i < 20; $i++) {
            $winnersName[] = $user->json()[$i]['firstname'] . ' ' . $user->json()[$i]['lastname'];
        }

        // clear the database
        Goodies::all()->each->delete();

        Log::info('Gagnant.e.s du '.Carbon::now().' : '.json_encode($winnersName));

        // add the winners in the database
        for ($i = 0; $i < 20; $i++) {
            $entry = new Goodies();
            $entry->name = $winnersName[$i];
            $entry->save();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Générer les vainqueurs')
                ->label('Générer les vainqueurs')
                ->color('danger')
                ->action(fn() => $this->generateWinners())
        ];
    }
}
