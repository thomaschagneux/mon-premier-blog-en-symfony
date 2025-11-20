<?php

namespace App\Service\DataTable;

use App\Components\DataTables\Column;
use App\Components\DataTables\DataTablesComponent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * Class AbstractDataTableService
 * Provides reusable functionalities for creating and rendering DataTables with customizable columns and rows.
 */
abstract class AbstractDataTableService
{
    /**
     * @var Environment the Twig environment used for rendering templates
     */
    protected Environment $twig;

    /**
     * @var UrlGeneratorInterface the URL generator for creating links
     */
    protected UrlGeneratorInterface $urlGenerator;

    /**
     * @var array<int, array<string, mixed>> the configuration of table columns
     */
    protected array $columns = [];

    /**
     * @var array<string, string> a mapping of column keys to their titles
     */
    protected array $columnMappings = [];

    /**
     * AbstractDataTableService constructor.
     *
     * @param Environment           $twig         the Twig environment
     * @param UrlGeneratorInterface $urlGenerator the URL generator
     */
    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        private readonly TranslatorInterface $translator,
    ) {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->initializeColumns();
    }

    abstract protected function getEntityName(): string;

    /**
     * @return array<Column>
     */
    abstract protected function getColumnsConfig(): array;

    /**
     * @return array<int, object>
     */
    abstract protected function getData(): array;

    protected function initializeColumns(): void
    {
        $this->columns = [];
        foreach ($this->getColumnsConfig() as $column) {
            $this->columns[] = [
                'title' => $this->translator->trans($this->getEntityName().'.property.'.$column->getKey()),
                'key' => $column->getKey(),
                'formatter' => $column->getFormatter(),
                'class' => $column->getClass(),
            ];
        }
    }

    protected function createDataTableComponent(): DataTablesComponent
    {
        return new DataTablesComponent($this->columns);
    }

    public function renderTableContent(): string
    {
        $data = $this->getData();
        $rows = [];
        foreach ($data as $item) {
            $row = [];
            foreach ($this->getColumnsConfig() as $column) {
                $key = $column->getKey();
                $formatter = $column->getFormatter();
                $value = $formatter ? $formatter($item) : ($item->{$key} ?? '');
                $row[$key] = $value;
            }
            $rows[] = $row;
        }

        return $this->renderTable($rows);
    }

    public function renderTable(array $rows): string
    {
        $table = $this->createDataTableComponent();
        foreach ($rows as $row) {
            $table->addRow($row);
        }

        return $table->render($this->twig);
    }

    /**
     * Generates an HTML link.
     *
     * @param string      $route  the route name
     * @param string      $label  the link label
     * @param string|null $class  the CSS class for the link
     * @param array       $params parameters for the route
     *
     * @return string the generated link HTML
     */
    protected function getLink(string $route, string $label, ?string $class = null, array $params = []): string
    {
        $url = $this->urlGenerator->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_PATH);
        $class = $class ? 'class="'.$class.'"' : '';

        return sprintf('<a href="%s" %s>%s</a>', $url, $class, $label);
    }

    /**
     * Generates an HTML "Edit" link.
     *
     * @param string $route  the route name
     * @param array  $params parameters for the route
     *
     * @return string the generated "Edit" link HTML
     */
    protected function getEditLink(string $route, array $params = []): string
    {
        return $this->getLink($route, 'Modifier', 'btn btn-sm btn-warning', $params);
    }

    /**
     * Generates an HTML "Delete" link.
     *
     * @param string $route  the route name
     * @param array  $params parameters for the route
     *
     * @return string the generated "Delete" link HTML
     */
    protected function getDeleteLink(string $route, array $params = []): string
    {
        return $this->getLink($route, 'Supprimer', 'btn btn-sm btn-danger', $params);
    }

    /**
     * Generates an HTML "Show" link.
     *
     * @param string $route  the route name
     * @param array  $params parameters for the route
     *
     * @return string the generated "Show" link HTML
     */
    protected function getShowLink(string $route, array $params = []): string
    {
        return $this->getLink($route, 'Voir', 'btn btn-sm btn-primary', $params);
    }
}
