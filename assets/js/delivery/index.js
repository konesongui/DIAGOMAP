"use strict";
// Class definition

// Set all the required variables for the following methods
var dtID = 'deliveryDatatable',
    remoteAJAXFunctions = {
        loadData: 'admin/deliveryitem/data',
        reject: 'admin/deliveryitem/reject',
        validate: 'admin/deliveryitem/validate',
        view: 'admin/deliveryitem/view',
        edit: 'admin/deliveryitem/edit',
        print: 'admin/deliveryitem/print',
        sendEmail: 'admin/deliveryitem/sendEmail',
        partialDelivery: 'admin/deliveryitem/partialDelivery',
        completeDelivery: 'admin/deliveryitem/completeDelivery',
        cancelDelivery: 'admin/deliveryitem/cancelDelivery'
    };

$(document).ready(function() {

    // Filter the row by status ('active' field)
    $(document).on('change', `#statusFilter`, function(e) {
        // Desable default event
        e.preventDefault();
        deliveryTable.ajax.reload(null, false); // Reload data
    });

    // Configuration de base pour SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    // Initialisation de DataTables
    var deliveryTable = $('.'+ dtID).DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": baseurl + remoteAJAXFunctions.loadData,
            "type": "POST",
            data: function(d) {
                d.status = $("#statusFilter").val();
            },
        },
        "columns": [
            { "data": "delivery_number" },
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
                data: "dates.delivery_date",
                render: function(data, type, row) {
                    return `<div>
                        <span>Émis le: ${row.dates.delivery_date}</span><br>
                        <small>Valide jusqu'au: ${row.dates.deadline}</small>
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
                deliveryable: false,
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
                                <a class="dropdown-item view-delivery" href="#" data-id="${row.id}">
                                    <i class="fa fa-eye me-2"></i> Voir
                                </a>
                            </li>`;
                    
                    // Option Imprimer
                    actions += `
                            <li>
                                <a class="dropdown-item print-delivery" href="#" data-id="${row.id}">
                                    <i class="fa fa-print me-2"></i> Imprimer
                                </a>
                            </li>`;
                    
                    // Option Envoyer par email
                    actions += `
                            <li>
                                <a class="dropdown-item send-delivery" href="#" data-id="${row.id}">
                                    <i class="fa fa-envelope-o me-2"></i> Envoyer par email
                                </a>
                            </li>`;
                    
                    // Options Valider/Rejeter (uniquement pour les bon de livraison en attente)
                    if(parseInt(row.status.code) === 1) {
                        actions += `
                            <li>
                                <a class="dropdown-item partial-delivery" href="#" data-id="${row.id}">
                                    <i class="fa fa-truck me-2"></i> Livraison partielle
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item complete-delivery" href="#" data-id="${row.id}">
                                    <i class="fa fa-check-circle me-2"></i> Livraison complète
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item cancel-delivery" href="#" data-id="${row.id}">
                                    <i class="fa fa-ban me-2"></i> Annuler livraison
                                </a>
                            </li>`;
                    }

                     // Options Valider/Rejeter (uniquement pour les bon de livraison en attente)
                     if(parseInt(row.status.code) === 6) {
                        actions += `
                            <li>
                                <a class="dropdown-item partial-delivery" href="#" data-id="${row.id}">
                                    <i class="fa fa-truck me-2"></i> Livraison partielle
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item complete-delivery" href="#" data-id="${row.id}">
                                    <i class="fa fa-check-circle me-2"></i> Livraison complète
                                </a>
                            </li>`;
                    }

                    actions += `</ul></div>`;
                    return actions;
                }
            }
        ],
        "delivery": [[3, "desc"]],
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
        deliveryTable.ajax.reload(null, false);
    }


    // Voir un bon de livraison
    $('.' + dtID).on('click', '.view-delivery', function() {
        var deliveryId = $(this).data('id');
        window.location.href = base_url + '/'+ remoteAJAXFunctions.view  + '/'+ deliveryId;
    });

    // Modifier un bon de livraison
    $('.' + dtID).on('click', '.partial-delivery', function() {
        var deliveryId = $(this).data('id');
        window.location.href = base_url + '/'+ remoteAJAXFunctions.partialDelivery + '/'+ deliveryId;
    });

    
    // Confirmation avant envoi par email
    $('.' + dtID).on('click', '.send-delivery', function(e) {
        e.preventDefault();
        var deliveryId = $(this).data('id');
        
        Swal.fire({
            title: 'Envoi du bon de livraison par email',
            text: "Voulez-vous envoyer ce bon de livraison par email ?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, envoyer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + '/'+ remoteAJAXFunctions.sendEmail,
                    type: 'POST',
                    data: {
                        id: deliveryId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if(response.status == "success") {
                            Toast.fire({
                                icon: 'success',
                                title: 'Bon de livraison envoyé avec succès'
                            });
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message || 'Erreur lors de l\'envoi'
                            });
                        }
                    },
                    error: function() {
                        Toast.fire({
                            icon: 'error',
                            title: 'Erreur lors de l\'envoi'
                        });
                    }
                });
            }
        });
    });

    // Livraison complète
    $('.' + dtID).on('click', '.complete-delivery', function() {
        var deliveryId = $(this).data('id');
        
        Swal.fire({
            title: 'Confirmer la livraison complète',
            text: "Voulez-vous marquer cette commande comme totalement livrée ?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, confirmer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Traitement en cours...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: base_url + '/' + remoteAJAXFunctions.completeDelivery,
                    type: 'POST',
                    data: {
                        id: deliveryId
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if(response.status == "success") {
                            Toast.fire({
                                icon: 'success',
                                title: 'Livraison complète enregistrée'
                            });
                            refreshTable();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message || 'Erreur lors de l\'opération'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        Toast.fire({
                            icon: 'error',
                            title: 'Erreur: ' + (xhr.responseJSON?.message || 'Erreur serveur')
                        });
                    }
                });
            }
        });
    });

    // Annulation de livraison
    $('.' + dtID).on('click', '.cancel-delivery', function() {
        var deliveryId = $(this).data('id');
        
        Swal.fire({
            title: 'Confirmer l\'annulation',
            text: "Voulez-vous vraiment annuler cette livraison ?",
            icon: 'warning',
            input: 'textarea',
            inputPlaceholder: 'Motif de l\'annulation',
            inputAttributes: {
                'aria-label': 'Motif de l\'annulation'
            },
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, annuler',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                if (!result.value.trim()) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Veuillez saisir un motif d\'annulation'
                    });
                    return;
                }

                Swal.fire({
                    title: 'Annulation en cours...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: base_url + '/' + remoteAJAXFunctions.cancelDelivery,
                    type: 'POST',
                    data: {
                        reason: result.value,
                        id: deliveryId
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if(response.status == "success") {
                            Toast.fire({
                                icon: 'success',
                                title: 'Livraison annulée avec succès'
                            });
                            refreshTable();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message || 'Erreur lors de l\'annulation'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        Toast.fire({
                            icon: 'error',
                            title: 'Erreur: ' + (xhr.responseJSON?.message || 'Erreur serveur')
                        });
                    }
                });
            }
        });
    });


     // Imprimer une facture
     $('.' + dtID).on('click', '.print-delivery', function(e) {
        // console.log('print-delivery');
        e.preventDefault();
        var deliveryId = $(this).data('id');

        // console.log(deliveryId);
        
        if(deliveryId > 0) {
            Swal.fire({
                title: 'Chargement...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                type: "POST",
                url: base_url + remoteAJAXFunctions.print,
                data: {
                    id: deliveryId
                },
                dataType: "JSON",
                success: function(response) {
                    Swal.close();
                    Popup(response.page);
                },
                error: function(xhr) {
                    Swal.close();
                    Swal.fire({
                        title: 'Erreur',
                        text: xhr.responseJSON?.message || 'Erreur serveur lors de l\'impression',
                        icon: 'error'
                    });
                }
            });
        } else {
            Swal.fire({
                title: 'Attention',
                text: 'Veuillez sélectionner un bon de livraison',
                icon: 'warning'
            });
        }
    });

    // Fonction pour imprimer un bon de livraison
    function Popup(data) {
        try {
            var frame1 = $('<iframe />');
            frame1[0].name = "frame1";
            frame1.css({
                'position': 'fixed',
                'top': '-9999px',
                'left': '-9999px'
            });
            $("body").append(frame1);
            
            var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : 
                          frame1[0].contentDocument.document ? frame1[0].contentDocument.document : 
                          frame1[0].contentDocument;
            
            frameDoc.document.open();
            frameDoc.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Impression bon de livraison</title>
                    <meta charset="utf-8">
                    <style>
                        /* Reset et base */
                        body {
                            margin: 0;
                            padding: 0;
                            background-color: white;
                            font-family: Arial, sans-serif;
                            line-height: 1.4;
                            color: #000;
                        }
                        
                        /* Configuration pour l'impression A4 */
                        @page {
                            size: A4;
                            margin: 15mm 10mm;
                            @top-center { content: element(pageHeader); }
                            @bottom-center { content: element(pageFooter); }
                        }
                        
                        /* Force l'impression des couleurs et images */
                        * {
                            -webkit-print-color-adjust: exact !important;
                            color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                        
                        /* Dimensions A4 en pixels (approx. 210mm x 297mm) */
                        .page-a4 {
                            width: 210mm;
                            margin: 0 auto;
                            padding: 15mm 10mm;
                            box-sizing: border-box;
                            position: relative;
                            background: white;
                            box-shadow: 0 0 5px rgba(0,0,0,0.1);
                        }
                        
                        /* Pour éviter les coupures malheureuses */
                        table, img, div {
                            page-break-inside: avoid;
                        }
                        
                        /* Gestion des sauts de page */
                        .page-break {
                            page-break-after: always;
                        }
                        
                        .no-print {
                            display: none !important;
                        }
                        
                        @media print {
                            body, .page-a4 {
                                background: white;
                                width: auto;
                                height: auto;
                                margin: 0;
                                padding: 0;
                                box-shadow: none;
                            }
                            
                            /* Masquer les éléments inutiles à l'impression */
                            .no-print, .no-print * {
                                display: none !important;
                            }
                            
                            /* Style d'impression spécifique */
                            .print-only {
                                display: block !important;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="page-a4">
                        ${data}
                    </div>
                    <script>
                        // Ajustements finaux avant impression
                        window.onload = function() {
                            // Forcer le chargement des polices et images
                            setTimeout(function() {
                                window.focus();
                                window.print();
                            }, 500);
                        };
                    </script>
                </body>
                </html>
            `);
            frameDoc.document.close();
            
            setTimeout(function() {
                try {
                    window.frames["frame1"].focus();
                    window.frames["frame1"].print();
                } catch (e) {
                    console.error("Erreur lors de l'impression:", e);
                    Swal.fire({
                        title: 'Erreur',
                        text: 'Erreur lors de l\'impression. Veuillez réessayer.',
                        icon: 'error'
                    });
                } finally {
                    setTimeout(function() {
                        frame1.remove();
                    }, 1000);
                }
            }, 1000);
            
            return true;
        } catch (e) {
            console.error("Erreur lors de la création de la fenêtre d'impression:", e);
            Swal.fire({
                title: 'Erreur',
                text: 'Erreur lors de la préparation de l\'impression. Veuillez réessayer.',
                icon: 'error'
            });
            return false;
        }
    }


});