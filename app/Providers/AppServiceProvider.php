<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\PromoCode\Contracts\PromoCodeRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EloquentPromoCodeRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PromoCodeRepositoryInterface::class, EloquentPromoCodeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
