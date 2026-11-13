<?php

return [
    'per_page' => 20,
    'page_sizes' => [10, 20, 50, 100],
    'styles' => true,
    'summary_text' => 'Records :from–:to of :total',

    'templates' => [
        'main' => <<<'HTML'
            <div class="r-pagination">
                <div class="r-pagination-size">
                    <span class="r-pagination-size-label">Per page</span>
                    {count}
                </div>
                {pager}
            </div>
            HTML,

        'pager' => <<<'HTML'
            <nav class="r-pagination-navigation" aria-label="Pagination">
                {first}{prev}{pages}{next}{last}
            </nav>
            HTML,

        'summary' => '<div class="r-pagination-summary">{summary}</div>',

        'count' => <<<'HTML'
            <select name="per_page" aria-label="Items per page" data-per-page="{per_page}" data-current-page="{current_page}" data-page-name="{page_name}">
                {options}
            </select>
            HTML,

        'option' => '<option value="{value}"{selected}>{value}</option>',

        'first' => '<a href="{url}" aria-label="First page">«</a>',
        'first_disabled' => '<span aria-disabled="true">«</span>',

        'prev' => '<a href="{url}" aria-label="Previous page">‹</a>',
        'prev_disabled' => '<span aria-disabled="true">‹</span>',

        'page' => '<a class="r-pagination-page" href="{url}">{page}</a>',
        'page_current' => '<span aria-current="page">{page}</span>',
        'gap' => '<span class="r-pagination-gap" aria-hidden="true">…</span>',

        'next' => '<a href="{url}" aria-label="Next page">›</a>',
        'next_disabled' => '<span aria-disabled="true">›</span>',

        'last' => '<a href="{url}" aria-label="Last page">»</a>',
        'last_disabled' => '<span aria-disabled="true">»</span>',
    ],
];
