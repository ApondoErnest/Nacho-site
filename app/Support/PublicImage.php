<?php

namespace App\Support;

final class PublicImage
{
    /**
     * @return array{src: string, webp: string|null}|null
     */
    public static function sources(string $relativePath): ?array
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');

        $absolute = public_path($relativePath);

        if (! is_file($absolute)) {
            $webpOnly = preg_replace('/\.(png|jpe?g)$/i', '.webp', $relativePath);

            if ($webpOnly !== null && is_file(public_path($webpOnly))) {
                return [
                    'src' => asset($webpOnly),
                    'webp' => null,
                ];
            }

            return null;
        }

        $webpPath = preg_replace('/\.(png|jpe?g)$/i', '.webp', $relativePath);
        $webpFile = $webpPath !== null ? public_path($webpPath) : null;

        return [
            'src' => asset($relativePath),
            'webp' => ($webpFile && is_file($webpFile)) ? asset($webpPath) : null,
        ];
    }

    public static function preferredUrl(string $relativePath): ?string
    {
        $sources = self::sources($relativePath);

        if ($sources === null) {
            return null;
        }

        return $sources['webp'] ?? $sources['src'];
    }

    public static function resolvePath(?string $pathOrUrl): ?string
    {
        if ($pathOrUrl === null || $pathOrUrl === '') {
            return null;
        }

        $path = $pathOrUrl;

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path = is_string($parsed) ? ltrim($parsed, '/') : '';
        }

        $path = ltrim(str_replace('\\', '/', $path), '/');

        return is_file(public_path($path)) ? $path : null;
    }

    /**
     * Resolve a public URL for a center photo using slug maps, legacy slug aliases, and DB featured_image.
     *
     * @param  array<string, string|null>|null  $extraPaths  Optional slug => path overrides for a page
     */
    public static function centerImageUrl(string $slug, ?string $featuredImage = null, ?array $extraPaths = null, string ...$mapKeys): ?string
    {
        $maps = config('center_images', []);
        $alternateSlug = self::alternateCenterSlug($slug);

        $candidates = [];

        if ($extraPaths !== null) {
            $candidates[] = $extraPaths[$slug] ?? null;
            if ($alternateSlug !== null) {
                $candidates[] = $extraPaths[$alternateSlug] ?? null;
            }
        }

        foreach ($mapKeys as $mapKey) {
            $map = $maps[$mapKey] ?? [];
            $candidates[] = $map[$slug] ?? null;
            if ($alternateSlug !== null) {
                $candidates[] = $map[$alternateSlug] ?? null;
            }
        }

        $candidates[] = $featuredImage;

        foreach (array_filter($candidates) as $candidate) {
            $resolved = self::resolvePath($candidate);

            if ($resolved !== null) {
                return self::preferredUrl($resolved);
            }
        }

        return null;
    }

    /**
     * Resolve a relative public path for a center photo (for Blade optimized-image).
     *
     * @param  array<string, string|null>|null  $extraPaths
     */
    public static function centerImagePath(string $slug, ?string $featuredImage = null, ?array $extraPaths = null, string ...$mapKeys): ?string
    {
        $maps = config('center_images', []);
        $alternateSlug = self::alternateCenterSlug($slug);

        $candidates = [];

        if ($extraPaths !== null) {
            $candidates[] = $extraPaths[$slug] ?? null;
            if ($alternateSlug !== null) {
                $candidates[] = $extraPaths[$alternateSlug] ?? null;
            }
        }

        foreach ($mapKeys as $mapKey) {
            $map = $maps[$mapKey] ?? [];
            $candidates[] = $map[$slug] ?? null;
            if ($alternateSlug !== null) {
                $candidates[] = $map[$alternateSlug] ?? null;
            }
        }

        $candidates[] = $featuredImage;

        foreach (array_filter($candidates) as $candidate) {
            $resolved = self::resolvePath($candidate);

            if ($resolved !== null) {
                return $resolved;
            }
        }

        return null;
    }

    private static function alternateCenterSlug(string $slug): ?string
    {
        if (str_starts_with($slug, 'novetesco-')) {
            return 'nacho-'.substr($slug, strlen('novetesco-'));
        }

        if (str_starts_with($slug, 'nacho-')) {
            return 'novetesco-'.substr($slug, strlen('nacho-'));
        }

        return null;
    }
}
