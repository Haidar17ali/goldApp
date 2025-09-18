<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\Relation;

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
        Paginator::useBootstrapFour();
        Relation::morphMap([
            'cutting' => \App\Models\CuttingDetail::class,
            'hutang' => \App\Models\DeliveryDetail::class,
            // tambah model lain kalau perlu
        ]);
    }
}
