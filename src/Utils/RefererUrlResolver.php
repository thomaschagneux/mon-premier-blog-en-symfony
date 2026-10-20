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

        $scheme = strtolower($parts['scheme']);
        if (!\in_array($scheme, ['http', 'https'], true)
            || $scheme !== strtolower($request->getScheme())
            || strcasecmp($parts['host'], $request->getHost()) !== 0
        ) {
            return $fallbackUrl;
        }

        $refererPort = $parts['port'] ?? ('https' === $scheme ? 443 : 80);
        if ($refererPort !== $request->getPort()) {
            return $fallbackUrl;
        }

        $path = $parts['path'];
        if (!str_starts_with($path, '/')
            || str_starts_with($path, '//')
            || str_contains($path, '\\')
            || preg_match('/[\x00-\x1F\x7F]/', $path)
        ) {
            return $fallbackUrl;
        }

        $url = $path.(isset($parts['query']) ? '?'.$parts['query'] : '');

        return $url === $request->getRequestUri() ? $fallbackUrl : $url;
    }
}
