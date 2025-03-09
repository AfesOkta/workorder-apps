/*
Template Name: Drezon - Responsive Bootstrap 4 Admin Dashboard
Author: Themesbrand
Version: 1.0.0
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Datatables Js File
*/

$(document).ready(function() {
    $('#datatable').DataTable();

    //Buttons examples
    var table = $('#datatable-buttons').DataTable({
        lengthChange: false,
        buttons: ['copy', 'excel', 'pdf', 'colvis']
    });

    table.buttons().container()
        .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');


    // Multi Selection Datatable
    $('#selection-datatable').DataTable({
        select: {
            style: 'multi'
        },
    });

    // Key Datatable
    $('#key-datatable').DataTable({
        keys: true
    });

    table.buttons().container()
        .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');


    // Alternative Pagination Datatable
    $('#alternative-page-datatable').DataTable({
        "pagingType": "full_numbers"
    });

    // Scroll Vertical Datatable
    $('#scroll-vertical-datatable').DataTable({
        "scrollY":        "350px",
        "scrollCollapse": true,
        "paging":         false
    });

    // Scroll horizontal Datatable
    $('#scroll-horizontal-datatable').DataTable({
        "scrollX": true
    });

    // Complex headers with column visibility Datatable
    $('#complex-header-datatable').DataTable({
        "columnDefs": [ {
            "visible": false,
            "targets": -1
        } ]
    });

    // Row created callback Datatable
    $('#row-callback-datatable').DataTable({
        "createdRow": function ( row, data, index ) {
            if ( data[5].replace(/[\$,]/g, '') * 1 > 150000 ) {
                $('td', row).eq(5).addClass('text-danger');
            }
        }
    });

    // State saving Datatable
    $('#state-saving-datatable').DataTable({
        stateSave: true
    });

} );