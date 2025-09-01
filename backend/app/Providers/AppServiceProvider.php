<?php

namespace App\Providers;

use App\Repositories\Contracts\MedicationRepositoryInterface;
use App\Repositories\Eloquent\EloquentMedicationRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind the medication repository interface to its Eloquent implementation
        $this->app->bind(
            MedicationRepositoryInterface::class,
            EloquentMedicationRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
