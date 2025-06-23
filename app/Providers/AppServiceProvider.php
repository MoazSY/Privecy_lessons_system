<?php

namespace App\Providers;


use App\Repositories\AdminRepositories;
use App\Repositories\AdminRepositoriesInterface ;
use App\Repositories\StudentRepositories;
use App\Repositories\StudentRepositoriesInterface;
use App\Repositories\TeacherRepositories;
use App\Repositories\TeacherRepositoriesInterface;
use App\Repositories\TokenRepositories;
use App\Repositories\TokenRepositoriesInterface;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(StudentRepositoriesInterface::class,StudentRepositories::class);
        $this->app->bind(AdminRepositoriesInterface::class,AdminRepositories::class);
        $this->app->bind(TokenRepositoriesInterface::class,TokenRepositories::class);
        $this->app->bind(TeacherRepositoriesInterface::class,TeacherRepositories::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
