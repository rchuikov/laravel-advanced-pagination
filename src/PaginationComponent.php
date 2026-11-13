<?php

namespace RChuikov\LaravelAdvancedPagination;

use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\Component;

final class PaginationComponent extends Component
{
    public function __construct(
        public LengthAwarePaginator $paginator,
        public string $section = 'controls',
        public array $templates = [],
    ) {}

    public function render(): View
    {
        return view('advanced-pagination::components.pagination');
    }
}
