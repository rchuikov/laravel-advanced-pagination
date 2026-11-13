<?php

namespace RChuikov\LaravelAdvancedPagination;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\HtmlString;
use InvalidArgumentException;

final class PaginationRenderer
{
    public function render(
        LengthAwarePaginator $paginator,
        string $section = 'controls',
        array $templates = [],
    ): Htmlable {
        $templates = array_replace(
            config('advanced-pagination.templates'),
            $templates,
        );

        return new HtmlString(match ($section) {
            'summary' => $this->renderSummary($paginator, $templates),
            'controls' => $this->renderControls($paginator, $templates),
            default => throw new InvalidArgumentException(
                "Unknown pagination section: {$section}"
            ),
        });
    }

    private function renderSummary(
        LengthAwarePaginator $paginator,
        array $templates,
    ): string {
        return strtr($templates['summary'], [
            '{summary}' => __(
                config('advanced-pagination.summary_text'),
                [
                    'from' => $paginator->firstItem() ?? 0,
                    'to' => $paginator->lastItem() ?? 0,
                    'total' => $paginator->total(),
                ],
            ),
        ]);
    }

    private function renderControls(
        LengthAwarePaginator $paginator,
        array $templates,
    ): string {
        if ($paginator->total() < min(config('advanced-pagination.page_sizes'))) {
            return '';
        }

        $parts = [
            '{count}' => $this->count($paginator, $templates),
            '{first}' => $this->navigation($paginator, 'first', 1, $templates),
            '{prev}' => $this->navigation(
                $paginator,
                'prev',
                max(1, $paginator->currentPage() - 1),
                $templates,
            ),
            '{pages}' => $this->pages($paginator, $templates),
            '{next}' => $this->navigation(
                $paginator,
                'next',
                min($paginator->lastPage(), $paginator->currentPage() + 1),
                $templates,
            ),
            '{last}' => $this->navigation(
                $paginator,
                'last',
                $paginator->lastPage(),
                $templates,
            ),
        ];

        $parts['{pager}'] = $paginator->hasPages()
            ? strtr($templates['pager'], $parts)
            : '';

        return strtr($templates['main'], $parts);
    }

    private function count(
        LengthAwarePaginator $paginator,
        array $templates,
    ): string {
        $options = '';

        foreach (config('advanced-pagination.page_sizes') as $size) {
            $options .= strtr($templates['option'], [
                '{value}' => (string) $size,
                '{selected}' => $size === $paginator->perPage() ? ' selected' : '',
            ]);
        }

        return strtr($templates['count'], [
            '{options}' => $options,
            '{per_page}' => (string) $paginator->perPage(),
            '{current_page}' => (string) $paginator->currentPage(),
            '{page_name}' => $paginator->getPageName(),
        ]);
    }

    private function navigation(
        LengthAwarePaginator $paginator,
        string $name,
        int $page,
        array $templates,
    ): string {
        if (! $paginator->hasPages()) {
            return '';
        }

        $disabled = $page === $paginator->currentPage();

        $template = $templates[$name.($disabled ? '_disabled' : '')];

        return strtr($template, ['{url}' => e($paginator->url($page))]);
    }

    private function pages(
        LengthAwarePaginator $paginator,
        array $templates,
    ): string {
        if (! $paginator->hasPages()) {
            return '';
        }

        $last = $paginator->lastPage();
        $current = $paginator->currentPage();

        $numbers = [1, $last];

        for ($page = max(1, $current - 2); $page <= min($last, $current + 2); $page++) {
            $numbers[] = $page;
        }

        $numbers = array_values(array_unique($numbers));
        sort($numbers);

        $html = '';
        $previous = 0;

        foreach ($numbers as $page) {
            if ($previous && $page - $previous > 1) {
                $html .= $templates['gap'];
            }

            $template = $templates[$page === $current ? 'page_current' : 'page'];

            $html .= strtr($template, [
                '{page}' => (string) $page,
                '{url}' => e($paginator->url($page)),
            ]);

            $previous = $page;
        }

        return $html;
    }
}
