<?php

namespace RChuikov\LaravelAdvancedPagination;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class PaginationAssets
{
    public static function url(string $file): string
    {
        $path = self::path($file);

        return route('advanced-pagination.asset', [
            'file' => $file,
            'hash' => self::hash($path),
        ]);
    }

    public function show(string $hash, string $file): BinaryFileResponse
    {
        $path = self::path($file);

        abort_unless(hash_equals(self::hash($path), $hash), Response::HTTP_NOT_FOUND);

        return response()->file($path, [
            'Content-Type' => match ($file) {
                'pagination.css' => 'text/css; charset=UTF-8',
                'pagination.js' => 'application/javascript; charset=UTF-8',
            },
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private static function path(string $file): string
    {
        $directory = match ($file) {
            'pagination.css' => 'css',
            'pagination.js' => 'js',
            default => abort(Response::HTTP_NOT_FOUND),
        };

        $published = public_path('vendor/advanced-pagination/'.$file);

        if (is_file($published)) {
            return $published;
        }

        $path = __DIR__.'/../resources/'.$directory.'/'.$file;

        abort_unless(is_file($path), Response::HTTP_NOT_FOUND);

        return $path;
    }

    private static function hash(string $path): string
    {
        return substr(hash_file('sha256', $path), 0, 16);
    }
}
