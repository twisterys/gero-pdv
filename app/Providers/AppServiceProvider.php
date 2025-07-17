<?php

namespace App\Providers;

use App\Http\Resources\Api\commercial\CommercialResource;
use App\Http\Resources\Api\parfums\ArticleResource;
use App\Http\Resources\Api\parfums\DemandeTransfertResource;
use App\Models\TransactionStock;
use App\Observers\TrasnsactionStockObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(database_path('migrations/central'));
        TransactionStock::observe(TrasnsactionStockObserver::class);
        ArticleResource::withoutWrapping();
        CommercialResource::withoutWrapping();
        DemandeTransfertResource::withoutWrapping();
        Validator::extend('without_spaces', function($attr, $value){
            return preg_match('/^\S*$/u', $value);
        });
    }
}
