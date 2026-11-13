# Laravel Advanced Pagination

Eloquent pagination with a page-size selector, an optional record summary, and customizable Blade controls.

## Installation

Requires PHP 8.5 and Laravel 13.

```bash
composer require rchuikov/laravel-advanced-pagination
```

## Usage

Paginate an Eloquent query and pass the result to your view.

```php
$posts = Post::query()->orderBy('id')->advancedPaginate();

return view('posts.index', compact('posts'));
```

```blade
<x-advanced-pagination :paginator="$posts" section="summary" />

@foreach ($posts as $post)
    <p>{{ $post->title }}</p>
@endforeach

<x-advanced-pagination :paginator="$posts" />
```

The summary is optional. The controls include a page-size selector and show navigation when there is more than one page. Pagination links preserve existing query parameters.

By default, `advancedPaginate()` reads the page size from `per_page` in the URL. To set a size explicitly, use `advancedPaginate(perPage: 50)`. Sizes outside `page_sizes` fall back to the configured default. To use a different page query parameter, pass `pageName: 'posts_page'`.

The Blade component also accepts a `LengthAwarePaginator` returned by Laravel's `paginate()`. Use a page size from `page_sizes` so the selector can display it.

## Configuration

Create `config/advanced-pagination.php` with the settings you want to override. Omitted settings use the defaults.

```php
<?php

return [
    'per_page' => 25,
    'page_sizes' => [10, 25, 50],
];
```

| Setting | Default | Description |
| --- | --- | --- |
| `per_page` | `20` | Default number of records per page. Include it in `page_sizes`. |
| `page_sizes` | `[10, 20, 50, 100]` | Sizes offered by the selector and accepted by `advancedPaginate()`. |
| `styles` | `true` | Set to `false` to use your own CSS. |
| `summary_text` | `Records :from–:to of :total` | Summary text. Translate it in your application's language files, keeping the `:from`, `:to`, and `:total` placeholders. |
| `templates` | Built-in templates | HTML overrides for individual parts of the component. |

### Customize the markup

Set individual keys under `templates` in the config to apply them throughout the app. To override a template for one component, pass `:templates`.

```blade
<x-advanced-pagination
    :paginator="$posts"
    :templates="['gap' => '<span>…</span>']"
/>
```

| Key | Available placeholders | Purpose |
| --- | --- | --- |
| `main` | `{count}`, `{pager}` | Main controls wrapper. |
| `pager` | `{first}`, `{prev}`, `{pages}`, `{next}`, `{last}` | Navigation wrapper and ordering of buttons. |
| `summary` | `{summary}` | Summary markup, rendered with `section="summary"`. |
| `count` | `{options}`, `{per_page}`, `{current_page}`, `{page_name}` | Page-size selector. |
| `option` | `{value}`, `{selected}` | Individual page-size option. `{selected}` is ` selected` for the active value, otherwise empty. |
| `first` | `{url}` | Link to the first page. |
| `first_disabled` | `{url}` | Disabled state of the first-page control. |
| `prev` | `{url}` | Link to the previous page. |
| `prev_disabled` | `{url}` | Disabled state of the previous-page control. |
| `page` | `{page}`, `{url}` | Individual numbered page link. |
| `page_current` | `{page}`, `{url}` | Current numbered page. |
| `gap` | None | Gap between nonadjacent page numbers. |
| `next` | `{url}` | Link to the next page. |
| `next_disabled` | `{url}` | Disabled state of the next-page control. |
| `last` | `{url}` | Link to the last page. |
| `last_disabled` | `{url}` | Disabled state of the last-page control. |

If you customize the selector markup, preserve its classes and data attributes so changing the page size still works.

## License

[MIT](LICENSE)
