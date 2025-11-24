<?php

namespace App\Components\DataTables;

class Column
{
    private string $key;

    private ?string $title = null;

    /**
     * @var callable|null
     */
    private $formatter;

    private ?string $class = null;

    public function __construct(
        string $key,
        ?callable $formatter,
        ?string $class,
    ) {
        $this->key = $key;
        $this->formatter = $formatter;
        $this->class = $class;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getFormatter(): ?callable
    {
        return $this->formatter;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }
}
