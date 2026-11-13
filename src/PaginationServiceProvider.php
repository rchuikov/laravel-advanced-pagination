<?php

namespace RChuikov\LaravelAdvancedPagination;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class PaginationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached()) {
            return;
        }

        $defaults = require __DIR__.'/../config/pagination.php';
        $config = $this->app->make('config');
        $overrides = $config->get('advanced-pagination', []);
        $settings = array_merge($defaults, $overrides);
        $settings['templates'] = array_merge(
            $defaults['templates'],
            $overrides['templates'] ?? [],
        );

        $config->set('advanced-pagination', $settings);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/assets.php');

        $this->publishes(
            [__DIR__.'/../config/pagination.php' => config_path('advanced-pagination.php')],
            'advanced-pagination-config',
        );

        $this->publishes([
            __DIR__.'/../resources/css/pagination.css' => public_path('vendor/advanced-pagination/pagination.css'),
            __DIR__.'/../resources/js/pagination.js' => public_path('vendor/advanced-pagination/pagination.js'),
        ], 'advanced-pagination-assets');

        $this->loadViewsFrom(
            __DIR__.'/../resources/views',
            'advanced-pagination',
        );

        Blade::component(
            PaginationComponent::class,
            'advanced-pagination',
        );

        Builder::macro('advancedPaginate', function (
            ?int $perPage = null,
            array $columns = ['*'],
            string $pageName = 'page',
            ?int $page = null,
        ): LengthAwarePaginator {
            /** @var Builder $this */
            $perPage ??= (int) request()->query(
                'per_page',
                config('advanced-pagination.per_page'),
            );

            if (! in_array($perPage, config('advanced-pagination.page_sizes'), true)) {
                $perPage = (int) config('advanced-pagination.per_page');
            }

            return $this->paginate(
                $perPage,
                $columns,
                $pageName,
                $page,
            )->withQueryString();
        });
    }
}
