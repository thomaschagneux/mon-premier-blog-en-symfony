import $ from 'jquery';
import './component/datatables/datatables.min.js';
import './component/datatables/datatables.min.css';


// Ensure jQuery is available globally for the plugin
window.$ = window.jQuery = $;

document.addEventListener('DOMContentLoaded', function () {
    const $tables = $('.datatables');
    if ($tables.length) {
        $tables.DataTable({

        });
    }
});
