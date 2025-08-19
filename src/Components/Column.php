<?php

namespace App\Components;

class Column
{
    public function __construct(
        private string $name,
        private string $title,
        private \Closure $data,
        private array $options = [],
    )
    {
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    public function getData($entity): mixed
    {
        return ($this->data)($entity);
    }
    public function getOptions(): array
    {
        return $this->options;
    }

    public static function make(string $name, string $title, \Closure $data, array $options = []): self
    {
        return new self($name, $title, $data, $options);
    }
}
