<?php

namespace App\Service;

use App\Entity\Picture;

class ImageHelper
{
    public const DEFAULT_FALLBACK = 'media/img/default_profile_picture.png';

    public function getPublicPath(?Picture $picture = null, ?string $src = null, ?string $default = null): string
    {
        $default = $default ?: self::DEFAULT_FALLBACK;

        if (null !== $src && '' !== trim($src)) {
            return $this->normalize($src);
        }

        if ($picture instanceof Picture) {
            $pathName = $picture->getPathName();
            $pathName = trim($pathName, '/ ');

            $fileName = $picture->getFileName();
            $fileName = trim($fileName, '/ ');

            if ('' !== $pathName && str_contains(basename($pathName), '.')) {
                return $this->normalize($pathName);
            }

            if ('' !== $pathName && '' !== $fileName) {
                return $this->normalize($pathName.'/'.$fileName);
            }

            if ('' === $pathName && '' !== $fileName) {
                return $this->normalize($fileName);
            }
        }

        return $this->normalize($default);
    }

    private function normalize(string $path): string
    {
        $path = trim($path);

        // Ne pas toucher aux URLs absolues ou protocoles spéciaux
        if (
            str_starts_with($path, 'http://')
            || str_starts_with($path, 'https://')
            || str_starts_with($path, '//')
            || str_starts_with($path, 'data:')
            || str_starts_with($path, 'blob:')
        ) {
            return $path;
        }

        // Chemins relatifs au projet/public
        $path = ltrim($path, '/');

        return preg_replace('#/{2,}#', '/', $path) ?? $path;
    }
}
