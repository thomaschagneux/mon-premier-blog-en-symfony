<?php

namespace App\Twig;

use App\Utils\StringManipulator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigFilters extends AbstractExtension
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly StringManipulator $stringManipulator,
    ) {
    }

    public function getFilters()
    {
        return [
            new TwigFilter('translate_enum', [$this, 'getTranslateEnum']),
            new TwigFilter('enum_translation_key', [$this, 'getEnumTranslationKey']),
        ];
    }

    /**
     * @throws \ReflectionException
     */
    public function getTranslateEnum(\BackedEnum $enum, string $domain = 'messages'): string
    {
        return $this->translator->trans($this->getEnumTranslationKey($enum), [], $domain);
    }

    /**
     * @throws \ReflectionException
     */
    public function getEnumTranslationKey(\BackedEnum $enum): string
    {
        $reflexion = new \ReflectionEnum($enum::class);
        $enumName = $this->stringManipulator->toSnakeCase($reflexion->getShortName());
        $value = (string) $enum->value;

        return sprintf('enums.%s.%s', $enumName, $value);
    }
}
