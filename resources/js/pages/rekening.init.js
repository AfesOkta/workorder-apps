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
            url: "/master/rekening/list-rekening"
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
                data: "kode_rekening",
                name: "kode_rekening",
                searchable: true,
                orderable: true,
                className: "tdMiddleLeft"
            },
            {
                data: "uraian",
                name: "uraian",
                className: "tdMiddleLeft wrap-text",
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
            { visible: false, targets: [0] },
            { target: 2, "min-width": "250px" }
        ],
        order: [[1, "asc"]]
    });
});
