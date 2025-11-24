<?php

namespace App\Components\DataTables;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class DataTablesComponent
{
    /**
     * @var array<array<string, string>>
     */
    private array $rows = [];

    /**
     * @var array<Column>
     */
    private array $columns = [];

    /**
     * @param array<Column> $columns
     */
    public function __construct(
        array $columns,
    ) {
        $this->columns = $columns;
    }

    /**
     * @param array<string, string> $row
     *
     * @return $this
     */
    public function addRow(array $row): static
    {
        $this->rows[] = $row;

        return $this;
    }

    /**
     * @return array<array<string, string>>
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @return array<Column>
     */
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
