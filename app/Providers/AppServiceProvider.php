<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\URL;

use App\Models\Marketplace;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

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
            'debt.cut' => \App\Models\DeliveryDetail::class,
            // tambah model lain kalau perlu
        ]);

        // for production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        Event::listen(BuildingMenu::class, function (BuildingMenu $event) {

            if (! $event->menu->itemKeyExists('penjualan-online')) {
                return;
            }

            foreach (Marketplace::where('is_active', true)->orderBy('name')->get() as $marketplace) {

                $event->menu->addIn('penjualan-online', [

                    'text' => $marketplace->name,
                    'icon' => 'fas fa-store',
                    'url'  => 'gold-app/online/penjualan/' . $marketplace->id,
                    'can'  => 'penjualan.online.index',

                ]);
            }
        });
    }
}
