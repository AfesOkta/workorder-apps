const { post } = require("jquery");
const { method } = require("lodash");

$(function() {
    "use strict";

    $("#datatable")
        .DataTable()
        .destroy();
    const table = $("#datatable").DataTable({
        bFilter: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: "/master/kegiatan/list-kegiatan"
        },
        rowReorder: {
            selector: "td:nth-child(4)"
        },
        autowidth: true,
        responsive: true,
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: ""
            },
            {
                data: "tahun",
                name: "tahun",
                searchable: true,
                orderable: true,
                className: "tdMiddleLeft"
            },
            { data: "lokasi", searchable: true, orderable: false },
            {
                data: "subkegiatan_kode",
                name: "subkegiatan_kode",
                className: "tdLeft",
                searchable: true,
                orderable: true
            },
            {
                data: "subkegiatan_nama",
                name: "subkegiatan_nama",
                className: "tdmiddleLeft wrap-text",
                searchable: false,
                orderable: true
            },
            {
                data: "action",
                className: "tdCenter",
                searchable: false,
                orderable: false
            }
        ],
        rowReorder: {
            selector: "td:nth-child(4)"
        },
        autowidth: true,
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRowImmediate,
                type: "column",
                target: "tr"
            },
            breakpoints: [
                { name: "desktop", width: Infinity },
                { name: "large", width: 1920 },
                { name: "tablet", width: 1024 },
                { name: "fablet", width: 768 },
                { name: "phone", width: 480 }
            ]
        },
        columnDefs: [
            { visible: false, targets: [0, 2] },
            { target: 4, "min-width": "150px" }
        ],
        order: [[1, "asc"]],
        drawCallback: function(settings) {
            var api = this.api();
            var rows = api.rows({ page: "current" }).nodes();
            var last = null;
            api.column(2, { page: "current" })
                .data()
                .each(function(group, i) {
                    if (last !== group) {
                        $(rows)
                            .eq(i)
                            .before(
                                '<tr class="group"><td colspan="5"><h5><b>OPD ' +
                                    group +
                                    "</b></h5></td></tr>"
                            );
                        last = group;
                    }
                });
        }
    });

    // Event listener untuk filter
    $("#filter-button").on("click", function() {
        table.ajax.reload(); // Reload data tabel setelah filter diterapkan
    });
});
