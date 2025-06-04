"use strict";
// Class definition

// Set all the required variables for the following methods
var dtID = 'stockRemovalDatatable',
  remoteAJAXFunctions = {
      loadData: 'admin/stockremoval/data',
  };



$(document).ready(function() {
    // Initialisation de DataTables
    var stockRemovalTable = $('.'+ dtID).DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": baseurl + remoteAJAXFunctions.loadData,
            "type": "POST"
        },
        "columns": [
            { "data": "reference" },
            { "data": "origin" },
            { "data": "reason" },
            { "data": "date" },
            { 
                "data": "montant",
                "className": "text-right"
            },
            { 
                "data": "actions",
                "orderable": false,
                "searchable": false,
                "className": "text-right"
            }
        ],
        "order": [[2, "desc"]], // Tri par date décroissante
        "language": {
            "url": baseurl + "assets/js/french.json"
        },
        "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "pageLength": 25
    });
});