<?php

namespace App\Filament\Admin\Resources\GoodiesResource\Pages;

use App\Filament\Admin\Resources\GoodiesResource;
use App\Models\Goodies;
use App\Models\User;
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

        $wallets=array_map(fn($item)=>$item['rows'][0]['payments'][0]['wallet_id'], $accumulatedData);
        $uniqueWallets=array_unique($wallets);
        $uniqueWallets=array_values($uniqueWallets);
        $length = count($uniqueWallets);

        $membresPic = User::where('role','!=','none')->get('email')->toArray();

        $winners = [];
        $winnersName = [];
        while (count($winners) < 20) {
            $randomIndex = rand(0, $length - 1);
            $walletId = $uniqueWallets[$randomIndex];
            if (!in_array($walletId, $winners)) {
                $winners[] = $walletId;
            }

            $user = Http::withHeaders(
                [
                    'X-Return-Structure' => 'array',
                    'Content-Type' => 'application/json',
                    'X-API-KEY' => config('app.proxy_key')
                ]
            )->post(
                config('app.proxy_url'),
                ['wallets' => [$walletId]]
            );

            $userData = $user->json();
            Log::info($userData);
            $winnerMail = $userData[0]['email'];

            if (!in_array($winnerMail, $membresPic)) {
                $winnersName[] = $userData[0]['firstname'] . ' ' . $userData[0]['lastname'];
            }

            unset($uniqueWallets[$randomIndex]);
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
