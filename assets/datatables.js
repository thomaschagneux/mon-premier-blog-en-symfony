import $ from 'jquery';
import 'datatables.net';

// Ensure jQuery is available globally for the plugin
window.$ = window.jQuery = $;

document.addEventListener('DOMContentLoaded', function () {
    const $tables = $('.datatables');
    if ($tables.length) {
        $tables.DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json'
            },
            order: [[1, 'asc']]
        });
    }
});
