const { post } = require("jquery");
const { method } = require("lodash");

$(function () {
    "use strict";

    $("#datatable").DataTable().destroy();
    const table = $("#datatable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/master/skpd/list",
            data: function (d) {
                d.kode_skpd = $("#kd_skpd").val();
                d.nama_skpd = $("#nama_skpd").val();
            },
        },
        columns: [
            { data: "id", name: "id", title: "ID" },
            { data: "kd_skpd", name: "kode_skpd", title: "Kode SKPD" },
            { data: "nama_skpd", name: "nama_skpd", title: "Nama SKPD" },
            {
                data: null,
                name: "actions",
                title: "Actions",
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    let actions = "";
                    actions += `<a href="{{ route('master.skpd.create.skpd.blud', ['id' => '']) }}${btoa(
                        row.id
                    )}" class="btn btn-primary mr-1">Add BLUD</a>`;
                    actions +=
                        `<a href="/master/skpd/create-anggaran?id=` +
                        btoa(row.id) +
                        `" class="btn btn-warning mr-1">Add Rekening</a>`;
                    actions += `<a href="{{ route('master.skpd.create.bendahara', ['id' => '']) }}${btoa(
                        row.id
                    )}" class="btn btn-info mr-1">Add Bendahara</a>`;
                    actions += `<a href="{{ route('master.skpd.create.otorisator', ['id' => '']) }}${btoa(
                        row.id
                    )}" class="btn btn-success mr-1">Add Otorisator</a>`;
                    actions += `<a href="javascript:void(0)" onclick="delete(${row.id})" class="btn btn-danger mr-1">Delete</a>`;
                    return actions;
                },
            },
        ],
        autowidth: true,
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRowImmediate,
                type: "column",
                target: "tr",
            },
            breakpoints: [
                { name: "desktop", width: Infinity },
                { name: "large", width: 1920 },
                { name: "tablet", width: 1024 },
                { name: "fablet", width: 768 },
                { name: "phone", width: 480 },
            ],
        },
    });

    // Event listener untuk filter
    $("#filter-button").on("click", function () {
        table.ajax.reload(); // Reload data tabel setelah filter diterapkan
    });
});
