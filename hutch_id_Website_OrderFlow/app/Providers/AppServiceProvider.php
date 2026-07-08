<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Pesanan;

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
        \Carbon\Carbon::setLocale('id');

        if (config('app.env') === 'production' || env('APP_ENV') === 'production') {
            \URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            $jumlahMenunggu = Pesanan::where('status', 'menunggu_konfirmasi')->count();
            $view->with('jumlahMenunggu', $jumlahMenunggu);
        });
    }
}
