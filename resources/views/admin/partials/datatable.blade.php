<style>
    .dataTables_wrapper>.row {
        justify-content: center;
        align-items: center;
    }

    .dataTables_wrapper .dt-buttons {
        margin-bottom: 10px;
    }

    .dt_table tr td,
    .dt_table tr th {
        vertical-align: middle;
    }

    .dt_table .table-img {
        width: 40px;
        height: 40px;
        object-fit: cover;
    }
</style>
<script src="{{ get_asset('admin_assets') }}/libs/flatpickr/flatpickr.min.js"></script>
<script src="{{ get_asset('admin_assets') }}/js/dataTable_bundled.min.js"></script>
{{-- <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.60/pdfmake.min.js" integrity="sha256-DgMKT/pyAKjuP9wB3FRJa8IAVMWlWYjUFfd3UgSCtU0=" crossorigin="anonymous"></script> --}}
{{-- <script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.60/vfs_fonts.js" integrity="sha256-UsYCHdwExTu9cZB+QgcOkNzUCTweXr5cNfRlAAtIlPY=" crossorigin="anonymous"></script> --}}

<script>
    var dtable = $("table.dt_table").DataTable({
        scrollX: false,//!0,
        responsive: true,
        lengthMenu: [
            [50, 100, 200, -1],
            [50, 100, 200, "All"]
        ],
        "paging": false,
        "bInfo" : false,
        //"ordering": false,
        //"info": false,
        //"searching" : false,
        // buttons: ["copy", "print", "pdf"],
        language: {
            paginate: {
                previous: "<i class='mdi mdi-chevron-left'>",
                next: "<i class='mdi mdi-chevron-right'>"
            }
        },
        drawCallback: function() {
            $(".dataTables_paginate > .pagination").addClass("pagination-rounded")
        }
    });
    dtable.buttons().container().prependTo(".dataTables_wrapper .col-md-6:eq(0)");

    $(".human_datepicker").flatpickr({
        altInput: !0,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d"
    })
</script>

@if(isset($load_swtichery))
<link href="{{ get_asset('admin_assets') }}/libs/switchery/switchery.min.css" rel="stylesheet" type="text/css" />
<script src="{{ get_asset('admin_assets') }}/libs/switchery/switchery.min.js"></script>
<script>
    $('[data-toggle="switchery"]').each(function(a, e) {
        new Switchery($(this)[0], $(this).data())
    });

</script>
@endif
