<?php

namespace App\Components\DataTables;


class Column
{
    private string $key;

    /**
     * @var callable|null
     */
    private mixed $formatter = null;

    private ?string $class = null;

    public function __construct(
        string $key,
        ?callable $formatter,
        ?string $class,
    )
    {
        $this->key = $key;
        $this->formatter = $formatter;
        $this->class = $class;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getFormatter(): mixed
    {
        return $this->formatter;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

}
