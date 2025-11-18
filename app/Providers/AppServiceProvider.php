<?php

namespace App\Providers;

use App\Services\GoogleDriveService; // <-- Adăugat
use GuzzleHttp\Client as HttpClient; // <-- Adăugat
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Află dacă rulezi în local/testare
        $isDevelopment = $this->app->environment('local', 'testing');

        /**
         * Înregistrează clientul Guzzle (HttpClient) ca "singleton".
         * Asta înseamnă că Laravel va crea o singură instanță
         * a clientului și o va refolosi oriunde este injectată.
         */
        $this->app->singleton(HttpClient::class, function ($app) use ($isDevelopment) {
            return new HttpClient([
                // 'verify' => !$isDevelopment, // Poți decomenta asta pentru a opri verificarea SSL în local
                'timeout' => 30.0, // Timeout default de 30 secunde
            ]);
        });
        
        /**
         * Înregistrează serviciul tău GoogleDriveService.
         * Când codul tău cere (type-hint) GoogleDriveService,
         * Laravel se va uita la constructorul său, va vedea că
         * are nevoie de un HttpClient, va lua singleton-ul
         * definit mai sus și îl va injecta automat.
         */
        $this->app->singleton(GoogleDriveService::class, function ($app) {
            return new GoogleDriveService($app->make(HttpClient::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}