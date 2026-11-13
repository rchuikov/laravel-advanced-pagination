<?php

namespace RChuikov\LaravelAdvancedPagination\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use RChuikov\LaravelAdvancedPagination\PaginationAssets;
use RChuikov\LaravelAdvancedPagination\PaginationServiceProvider;

final class PaginationTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [PaginationServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', ':memory:');
    }

    public function test_partial_templates_preserve_defaults_and_can_be_overridden_in_blade(): void
    {
        config(['advanced-pagination.templates' => [
            'next' => '<a href="{url}">Configured next</a>',
        ]]);
        (new PaginationServiceProvider($this->app))->register();

        $paginator = new LengthAwarePaginator(range(21, 40), 60, 20, 2, [
            'path' => '/posts',
            'pageName' => 'posts_page',
        ]);

        $html = Blade::render('<x-advanced-pagination :paginator="$paginator" />', compact('paginator'));

        self::assertStringContainsString('Configured next', $html);
        self::assertStringContainsString('data-page-name="posts_page"', $html);
        self::assertStringContainsString('href="/posts?posts_page=3"', $html);
        self::assertStringContainsString('aria-current="page">2', $html);

        $templates = ['next' => '<a href="{url}">Component next</a>'];
        $html = Blade::render(
            '<x-advanced-pagination :paginator="$paginator" :templates="$templates" />',
            compact('paginator', 'templates'),
        );

        self::assertStringContainsString('Component next', $html);
        self::assertStringNotContainsString('Configured next', $html);
    }

    public function test_macro_uses_allowed_sizes_and_preserves_query_parameters(): void
    {
        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
        });
        Post::query()->insert(array_map(fn (int $id) => ['id' => $id], range(1, 25)));

        Route::get('/posts', function (): array {
            $paginator = Post::query()->orderBy('id')->advancedPaginate(pageName: 'posts_page');

            return [
                'size' => $paginator->perPage(),
                'first_id' => $paginator->items()[0]->id,
                'next_url' => $paginator->nextPageUrl(),
            ];
        });

        $this->get('/posts?per_page=10&posts_page=2&status=active')
            ->assertOk()
            ->assertJsonPath('size', 10)
            ->assertJsonPath('first_id', 11)
            ->assertJsonPath('next_url', url('/posts').'?per_page=10&status=active&posts_page=3');

        $this->get('/posts?per_page=5')
            ->assertOk()
            ->assertJsonPath('size', 20);
    }

    public function test_controls_are_hidden_only_below_the_minimum_page_size(): void
    {
        config(['advanced-pagination.page_sizes' => [20, 5, 10]]);

        $belowMinimum = new LengthAwarePaginator(range(1, 4), 4, 20);
        $atMinimum = new LengthAwarePaginator(range(1, 5), 5, 20);

        $html = Blade::render('<x-advanced-pagination :paginator="$belowMinimum" />', compact('belowMinimum'));
        self::assertStringNotContainsString('<select', $html);

        $summary = Blade::render(
            '<x-advanced-pagination :paginator="$belowMinimum" section="summary" />',
            compact('belowMinimum'),
        );
        self::assertStringContainsString('Records 1–4 of 4', $summary);

        $html = Blade::render('<x-advanced-pagination :paginator="$atMinimum" />', compact('atMinimum'));
        self::assertStringContainsString('<select name="per_page"', $html);
    }

    #[DataProvider('assets')]
    public function test_bundled_assets_are_served_with_versioned_urls(string $file, string $contentType): void
    {
        $url = PaginationAssets::url($file);

        $this->get($url)
            ->assertOk()
            ->assertHeader('Content-Type', $contentType);

        $wrongVersion = preg_replace('~/[a-f0-9]{16}/~', '/0000000000000000/', $url);
        $this->get($wrongVersion)->assertNotFound();
    }

    public static function assets(): array
    {
        return [
            'CSS' => ['pagination.css', 'text/css; charset=UTF-8'],
            'JavaScript' => ['pagination.js', 'application/javascript; charset=UTF-8'],
        ];
    }
}

final class Post extends Model {}
