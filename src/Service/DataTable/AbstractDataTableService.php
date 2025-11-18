<?php
namespace App\Services\DataTables;

use App\Components\DataTables\DataTablesComponent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AbstractDataTableService
 * Provides reusable functionalities for creating and rendering DataTables with customizable columns and rows.
 */
abstract class AbstractDataTableService
{
    /**
     * @var Environment The Twig environment used for rendering templates.
     */
    protected Environment $twig;

    /**
     * @var UrlGeneratorInterface The URL generator for creating links.
     */
    protected UrlGeneratorInterface $urlGenerator;

    /**
     * @var array<int, array<string, mixed>> The configuration of table columns.
     */
    protected array $columns = [];

    /**
     * @var array<string, string> A mapping of column keys to their titles.
     */
    protected array $columnMappings = [];

    /**
     * AbstractDataTableService constructor.
     *
     * @param Environment $twig The Twig environment.
     * @param UrlGeneratorInterface $urlGenerator The URL generator.
     */
    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
    )
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->initializeColumns();
    }

    /**
     * Initializes the columns based on columnMappings.
     */
    protected function initializeColumns(): void
    {
        foreach ($this->columnMappings as $key => $title) {
            $this->columns[] = [
                'title'     => $title,
                'key'       => $key,
                'formatter' => $this->getColumnFormatter($key),
                'class'  => $this->getColumnClass($key),
            ];
        }
    }

    /**
     * Returns the CSS class for a given column key.
     *
     * @param string $key The column key.
     * @return string The CSS class name.
     */
    protected function getColumnClass(string $key): string
    {
        return 'column-' . $key;
    }

    /**
     * Returns a formatter callable for a given column key.
     *
     * @param string $key The column key.
     * @return callable|null The formatter callable, or null if none is defined.
     */
    protected function getColumnFormatter(string $key): ?callable
    {
        return null;
    }

    /**
     * Creates a new DataTablesComponent instance with the configured columns.
     *
     * @return DataTablesComponent The DataTables component instance.
     */
    protected function createDataTableComponent(): DataTablesComponent
    {
        return new DataTablesComponent($this->columns);
    }

    /**
     * Renders the DataTable with the given rows.
     *
     * @param array<int, array<string, mixed>> $rows The rows to render in the table.
     * @return string The rendered table HTML.
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderTable(array $rows): string
    {
        $table = $this->createDataTableComponent();
        foreach ($rows as $row) {
            $formattedRow = [];
            foreach ($this->columns as $column) {
                $key = $column['key'];
                $value = $row[$key] ?? '';
                if (is_callable($column['formatter'])) {
                    $value = call_user_func($column['formatter'], $value, $row);
                }
                $formattedRow[$key] = $value;
            }
            $table->addRow($formattedRow);
        }
        return $table->render($this->twig);
    }

    /**
     * Generates an HTML link.
     *
     * @param string $route The route name.
     * @param string $label The link label.
     * @param string|null $class The CSS class for the link.
     * @param array $params Parameters for the route.
     * @return string The generated link HTML.
     */
    protected function getLink(string $route, string $label, ?string $class = null, array $params = []): string
    {
        $url = $this->urlGenerator->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_PATH);
        $class = $class ? 'class="' . $class . '"' : '';
        return sprintf('<a href="%s" %s>%s</a>', $url, $class, $label);
    }

    /**
     * Generates an HTML "Edit" link.
     *
     * @param string $route The route name.
     * @param array $params Parameters for the route.
     * @return string The generated "Edit" link HTML.
     */
    protected function getEditLink(string $route, array $params = []): string
    {
        return $this->getLink($route, 'Modifier', 'btn btn-sm btn-warning', $params);
    }

    /**
     * Generates an HTML "Delete" link.
     *
     * @param string $route The route name.
     * @param array $params Parameters for the route.
     * @return string The generated "Delete" link HTML.
     */
    protected function getDeleteLink(string $route, array $params = []): string
    {
        return $this->getLink($route, 'Supprimer', 'btn btn-sm btn-danger', $params);
    }

    /**
     * Generates an HTML "Show" link.
     *
     * @param string $route The route name.
     * @param array $params Parameters for the route.
     * @return string The generated "Show" link HTML.
     */
    protected function getShowLink(string $route, array $params = []): string
    {
        return $this->getLink($route, 'Voir', 'btn btn-sm btn-primary', $params);
    }
}
