<?php

namespace App\Components\DataTables;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DataTablesComponent
{
    private array $rows = [];

    private array $columns = [];

    public function __construct(
        array $columns,
    ) {
        $this->columns = $columns;
    }

    public function addRow(array $row): static
    {
        $this->rows[] = $row;

        return $this;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    public function render(Environment $twig): string
    {
        return $twig->render('Component/DataTables/dataTables.html.twig', [
            'columns' => $this->getColumns(),
            'rows' => $this->getRows(),
        ]);
    }
}
