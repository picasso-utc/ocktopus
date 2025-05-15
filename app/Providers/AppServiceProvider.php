<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Responses\LogoutResponse;
use Livewire\Livewire;
use App\Filament\Admin\Resources\EventResource\Components\ListInscritsModal;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') !== 'local') {
            $this->app['request']->server->set('HTTPS', true);
        }
        Livewire::component('filament.admin.resources.event-resource.components.list-inscrits-modal', ListInscritsModal::class);
    }
}
