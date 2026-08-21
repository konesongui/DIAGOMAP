"use strict";
// Class definition

// Set all the required variables for the following methods
var dtID = 'orderDatatable',
    remoteAJAXFunctions = {
        loadData: 'admin/orderformitem_supplier/data',
        view: 'admin/orderformitem_supplier/view',
        print: 'admin/orderformitem_supplier/print',
    };

$(document).ready(function() {
    // Configuration de base pour SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    // Initialisation de DataTables
    var orderTable = $('.'+ dtID).DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": baseurl + remoteAJAXFunctions.loadData,
            "type": "POST"
        },
        "columns": [
            { "data": "order_number" },
            { 
                data: "customer",
                render: function(data) {
                    return `<div>
                        <strong>${data.name}</strong><br>
                        <small>${data.email}<br>${data.phone}</small>
                    </div>`;
                }
            },
            
            { 
                data: "dates.commande",
                render: function(data, type, row) {
                    return `<div>
                        <span>Émis le: ${row.dates.commande}</span><br>
                        <small>Valide jusqu'au: ${row.dates.valid_until}</small>
                    </div>`;
                }
            },
            { "data": "payment_terms",
                render: function(data) {
                    return `<div>
                        <strong>${data}</strong><br>
                    </div>`;
                } 
            },
            { "data": "delivery_location",
                render: function(data) {
                    return `<div>
                        <strong>${data}</strong><br>
                    </div>`;
                }
            },
            { "data": "dates.creation",
                render: function(data) {
                    return `<div>
                        <strong>${data}</strong><br>
                    </div>`;
                }
            },
            {
                data: "amount",
                render: function(data) {
                    return `<div>
                        <strong>TTC: ${data.ttc}</strong><br>
                        <small>HT: ${data.ht}</small>
                    </div>`;
                }
            },
            {
                data: "status",
                render: function(data) {
                    return `<span class="label ${data.class}">${data.label}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    let actions = `
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" 
                                id="dropdownMenu${row.id}" 
                                data-bs-toggle="dropdown" 
                                aria-expanded="false">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu${row.id}">`;
                    
                    // Option Voir
                    actions += `
                            <li>
                                <a class="dropdown-item view-order" href="#" data-id="${row.id}">
                                    <i class="fa fa-eye me-2"></i> Voir
                                </a>
                            </li>`;
                    
                    actions += `</ul></div>`;
                    return actions;
                }
            }
        ],
        "order": [[3, "desc"]],
        "language": {
            "url": baseurl + "assets/js/french.json"
        },
        "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "pageLength": 25
    });

    // Fonction pour rafraîchir la table
    function refreshTable() {
        orderTable.ajax.reload(null, false);
    }


    // Voir un devis
    $('.' + dtID).on('click', '.view-order', function() {
        var orderId = $(this).data('id');
        window.location.href = base_url + '/'+ remoteAJAXFunctions.view  + '/'+ orderId;
    });

    // Imprimer un devis
    $('.' + dtID).on('click', '.print-order', function() {
        var orderI = $(this).data('id');
        // Ouvrir dans une nouvelle fenêtre
        var printWindow = window.open(base_url + 'admin/orderformitem/print/' + orderI, '_blank');
        
        // Attendre que la page soit chargée
        printWindow.onload = function() {
            printWindow.print();
        };
    });


});