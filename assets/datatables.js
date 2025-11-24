import $ from 'jquery';
import 'datatables.net-bs5';
import 'datatables.net-responsive-bs5';
import './styles/dataTables.bootstrap5.min.css';
import './styles/responsive.bootstrap5.min.css';

window.$ = window.jQuery = $;

document.addEventListener('DOMContentLoaded', () => {
    const $tables = $('.datatables');
    if ($tables.length) {
        $tables.each(function () {
            this.classList.add('table', 'table-striped', 'table-bordered');
        });
        $tables.DataTable({
            responsive: true,
            // Langue française pour DataTables
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json'
            }
        });
    }
});
