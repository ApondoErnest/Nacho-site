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
}
