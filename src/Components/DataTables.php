<?php

namespace App\Components;

use Twig\Environment;

class DataTables
{
    /** @var Column[] */
    private array $columns;
    private array $entities;
    private array $options = [];

    public function __construct(
        private readonly Environment $twig,
    ) {
    }

    public function setColumns(array $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function setEntities(array $entities): self
    {
        $this->entities = $entities;

        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function render(): string
    {
        return $this->twig->render('components/datatable.html.twig', [
            'columns' => $this->columns,
            'entities' => $this->entities,
            'options' => $this->options,
        ]);
    }
}
