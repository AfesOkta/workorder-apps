/*
Template Name: Drezon - Responsive Bootstrap 4 Admin Dashboard
Author: Themesbrand
Version: 1.0.0
Website: https://themesbrand.com/
Contact: themesbrand@gmail.com
File: Ecommerce datatables Js File
*/


// datatable
$(document).ready(function () {
    $('.datatable').DataTable({
        'columnDefs': [{
            'targets': 0,
            'searchable': false,
            'orderable': false
         }],
         'order': [[1, 'asc']],
         "language": {
            "paginate": {
                "previous": "<i class='mdi mdi-chevron-left'>",
                "next": "<i class='mdi mdi-chevron-right'>"
            }
        },
        "drawCallback": function () {
            $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
        }

    });
});
