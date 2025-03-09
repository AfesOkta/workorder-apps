const { post } = require("jquery");
const { method } = require("lodash");

$(function() {
    "use strict";
    $("#skpd_id").select2({
        allowClear: true,
        placeholder: "Silahkan Pilih OPD"
    });

    $("#opd").select2({
        dropdownParent: $("#sync_anggaran"),
        allowClear: true,
        dropdownAutoWidth: true,
        placeholder: "Silahkan Pilih OPD",
        width: "100%"
    });

    $("#tahapan_id").select2({
        allowClear: true,
        placeholder: "Silahkan Pilih Tahapan",
        width: "100%"
    });

    $("#table-1")
        .DataTable()
        .destroy();
    const table = $("#table-1").DataTable({
        processing: true,
        serverSide: true,
        method: "get",
        ajax: {
            url: "/kontrol-data/realisasi-sts/search",
            data: function(d) {
                d.kode_kegiatan = $("#kode_kegiatan").val();
                d.kode_rekening = $("#kode_rekening").val();
                d.tahapan_id = $("#tahapan_id").val();
                d.skpd_id = $("#skpd_id").val();
            }
        },
        columns: [
            {
                data: "nama_skpd",
                name: "nama_skpd",
                searchable: false,
                orderable: false
            },
            {
                data: "kegiatan_nama_kode",
                name: "kegiatan_nama_kode",
                searchable: false,
                orderable: false
            },
            {
                data: "kode_rekening",
                name: "kode_rekening",
                searchable: false,
                orderable: false
            },
            {
                data: "uraian",
                name: "uraian",
                searchable: false,
                orderable: false
            },
            {
                data: "subkegiatan_skode",
                name: "subkegiatan_skode",
                searchable: false,
                orderable: false
            },
            {
                data: "nama",
                name: "nama",
                searchable: false,
                orderable: false
            },
            {
                data: "nominal",
                render: $.fn.dataTable.render.number(",", ".", 0, ""),
                name: "nominal",
                searchable: false,
                orderable: false,
                className: "tdEnd"
            },
            {
                data: "realisasi_bku",
                render: $.fn.dataTable.render.number(",", ".", 0, ""),
                name: "realisasi_bku",
                searchable: false,
                orderable: false,
                className: "tdEnd"
            },
            {
                data: "procentase_bku",
                render: $.fn.dataTable.render.number(",", ".", 0, ""),
                name: "procentase_bku",
                searchable: false,
                orderable: false,
                className: "tdEnd"
            }
        ],
        columnDefs: [
            {
                visible: false,
                targets: 0
            },
            {
                visible: false,
                targets: 1
            },
            {
                width: "500px",
                targets: 3
            },
            {
                width: "40px",
                targets: [4, 5, 6, 7, 8]
            }
        ],
        order: [[0, "asc"]],
        displayLength: 100,
        drawCallback: function(settings) {
            var api = this.api();
            var rows = api
                .rows({
                    page: "current"
                })
                .nodes();
            var last = null;

            api.column(0, {
                page: "current"
            })
                .data()
                .each(function(group, i) {
                    if (last !== group) {
                        $(rows)
                            .eq(i)
                            .before(
                                '<tr class="group"><td colspan="5"><b>' +
                                    group +
                                    "</b></td></tr>"
                            );

                        last = group;
                    }
                });

            api.column(1, {
                page: "current"
            })
                .data()
                .each(function(group, i) {
                    if (last !== group) {
                        $(rows)
                            .eq(i)
                            .before(
                                '<tr class="group text-danger"><td colspan="5"><b>' +
                                    group +
                                    "</b></td></tr>"
                            );

                        last = group;
                    }
                });
        },
        autowidth: false,
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
        searching: false
    });

    $("#skpd_id").on("change", function() {
        table.draw();
    });

    $("#tahapan_id").on("change", function() {
        table.draw();
    });

    $("#kode_kegiatan").on("change", function() {
        table.draw();
    });

    $("#kode_rekening").on("change", function() {
        table.draw();
    });
});
