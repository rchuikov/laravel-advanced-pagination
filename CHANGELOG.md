# Changelog

All notable changes to `rchuikov/laravel-advanced-pagination` are documented here.

## [1.0.1] - 2026-11-13

### Fixed

- Hide pagination controls when the total number of records is smaller than the minimum configured `page_sizes` value, regardless of the order of that array. The optional records summary remains visible.
- Improve mobile pagination by hiding non-current page numbers and ellipses on narrow screens while retaining first, previous, current, next, and last controls.

## [1.0.0] - 2026-11-13

### Added

- Initial release of Laravel Advanced Pagination for Laravel 13 and PHP 8.5.
- `advancedPaginate()` Eloquent query builder macro with configurable page sizes and preserved query parameters.
- Blade pagination component with separate summary and controls sections.
- Customizable HTML templates and default styles.
- Bundled, versioned CSS and JavaScript assets with optional publishing.
- Test coverage for configuration, pagination behavior, and asset delivery.

[1.0.1]: https://github.com/rchuikov/laravel-advanced-pagination/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/rchuikov/laravel-advanced-pagination/releases/tag/v1.0.0
