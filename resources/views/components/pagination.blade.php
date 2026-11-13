@if (config('advanced-pagination.styles', true))
    @once
        <link rel="stylesheet" href="{{ \RChuikov\LaravelAdvancedPagination\PaginationAssets::url('pagination.css') }}">
    @endonce
@endif

@if ($section === 'controls')
    @once
        <script src="{{ \RChuikov\LaravelAdvancedPagination\PaginationAssets::url('pagination.js') }}" defer></script>
    @endonce
@endif

{{ app(\RChuikov\LaravelAdvancedPagination\PaginationRenderer::class)->render($paginator, $section, $templates) }}
