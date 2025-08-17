document.addEventListener('DOMContentLoaded', function () {
    // Initialize DataTables with French localization
    const table = $('.datatables').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.8/i18n/fr-FR.json'
        },
        // Optional: add simple default ordering on the first non-index column
        order: [[1, 'asc']]
    });
});
