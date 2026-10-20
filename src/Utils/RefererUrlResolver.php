<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\Request;

final class RefererUrlResolver
{
    public static function resolve(Request $request, string $fallbackUrl): string
    {
        $referer = $request->headers->get('referer');
        if (null === $referer) {
            return $fallbackUrl;
        }

        $parts = parse_url($referer);
        if (!\is_array($parts)
            || !isset($parts['scheme'], $parts['host'], $parts['path'])
            || isset($parts['user'], $parts['pass'])
        ) {
            return $fallbackUrl;
        }

        if (!self::isSameOrigin($parts, $request)) {
            return $fallbackUrl;
        }

        $path = $parts['path'];
        if (!self::isSafePath($path)) {
            return $fallbackUrl;
        }

        $url = $path.(isset($parts['query']) ? '?'.$parts['query'] : '');

        return $url === $request->getRequestUri() ? $fallbackUrl : $url;
    }

    /**
     * @param array{scheme: string, host: string, path: string, port?: int} $parts
     */
    private static function isSameOrigin(array $parts, Request $request): bool
    {
        $scheme = strtolower($parts['scheme']);
        if (!\in_array($scheme, ['http', 'https'], true)
            || $scheme !== strtolower($request->getScheme())
            || 0 !== strcasecmp($parts['host'], $request->getHost())
        ) {
            return false;
        }

        $refererPort = $parts['port'] ?? ('https' === $scheme ? 443 : 80);

        return $refererPort === $request->getPort();
    }

    private static function isSafePath(string $path): bool
    {
        return str_starts_with($path, '/')
            && !str_starts_with($path, '//')
            && !str_contains($path, '\\')
            && !preg_match('/[\x00-\x1F\x7F]/', $path);
    }
}
