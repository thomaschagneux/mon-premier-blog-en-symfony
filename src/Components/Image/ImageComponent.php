<?php

namespace App\Components\Image;

use App\Entity\Picture;
use App\Entity\User;
use App\Service\ImageHelper;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(name: 'Image', template: 'Component/Image/image.html.twig')]
class ImageComponent
{
    public function __construct(
        private readonly ImageHelper $imageHelper,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public ?User $user = null;
    public ?Picture $picture = null;
    public ?string $src = null;
    public ?string $alt = null;
    public ?string $class = null;
    public ?string $default = null;

    public ?int $size = null;
    public ?int $width = null;
    public ?int $height = null;
    public bool $circle = false;
    public bool $cover = true;

    public function resolvedSrc(): string
    {
        $picture = $this->picture;
        if (null === $picture && $this->user instanceof User) {
            $picture = $this->user->getPicture();
        }

        return $this->imageHelper->getPublicPath($picture, $this->src, $this->default);
    }

    public function resolvedAlt(): string
    {
        if (null !== $this->alt && '' !== trim($this->alt)) {
            return $this->alt;
        }

        if ($this->user instanceof User) {
            return $this->translator->trans('users.profile.alt_picture').$this->user->getFullName();
        }

        return $this->translator->trans('common.image_alt');
    }

    public function cssClass(): string
    {
        $classes = [];
        if ($this->class) {
            $classes[] = trim($this->class);
        }
        if ($this->circle) {
            $classes[] = 'rounded-circle';
        }

        return trim(implode(' ', array_filter($classes)));
    }

    public function style(): string
    {
        $styles = [];

        $w = $this->width;
        $h = $this->height;
        if (null !== $this->size) {
            $w = $w ?? $this->size;
            $h = $h ?? $this->size;
        }

        if (null !== $w) {
            $styles[] = 'width: '.$w.'px';
        }
        if (null !== $h) {
            $styles[] = 'height: '.$h.'px';
        }

        $styles[] = 'max-width: 100%';
        $styles[] = 'max-height: 100%';
        if ($this->cover) {
            $styles[] = 'object-fit: cover';
        }

        return implode('; ', $styles).(empty($styles) ? '' : ';');
    }
}
