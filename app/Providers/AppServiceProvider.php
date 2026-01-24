<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\Lot;
use App\Models\Paiement;
use App\Models\Evaluation;
use Laravel\Sanctum\Sanctum;
use App\Observers\LotObserver;
use App\Models\PersonalAccessToken;
use App\Observers\PaiementObserver;
use Illuminate\Support\Facades\Date;
use App\Observers\EvaluationObserver;
use App\Observers\AttributionObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\AttributionLotPrestataire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Lot::observe(LotObserver::class);
        AttributionLotPrestataire::observe(AttributionObserver::class);
        Evaluation::observe(EvaluationObserver::class);
        Paiement::observe(PaiementObserver::class);
        Carbon::setLocale('fr');
        Date::setLocale('fr');
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
