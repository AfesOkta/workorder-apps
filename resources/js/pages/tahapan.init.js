const { post } = require("jquery");
const { method } = require("lodash");

$(function() {
    "use strict";

    $("#datatable")
        .DataTable()
        .destroy();
    const table = $("#datatable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/master/tahapan/list-tahapan"
        },
        columns: [
            { data: "id", name: "id", title: "ID" },
            { data: "nama", name: "nama", title: "Nama" },
            {
                data: null,
                name: "actions",
                title: "Actions",
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let actions = `<a href="{{ route('master.skpd.create.skpd.blud', ['id' => '']) }}${btoa(
                        row.id
                    )} )" class="btn btn-danger" data-title="Delete tahapan">Delete</a>`;
                    return actions;
                }
            }
        ],
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
        }
    });

    // Event listener untuk filter
    $("#filter-button").on("click", function() {
        table.ajax.reload(); // Reload data tabel setelah filter diterapkan
    });
});
