$(function () {
    var data_table = $('#data-table');
    if (data_table.length > 0) {
        $('#data-table').DataTable({
            "order": [[ 0, "desc" ]],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true
        });
    }
});