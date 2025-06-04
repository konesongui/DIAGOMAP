"use strict";
// Class definition

// Set all the required variables for the following methods
var dtID = 'quoteDatatable',
    remoteAJAXFunctions = {
        loadData: 'admin/quoteitem/data',
        reject: 'admin/quoteitem/reject',
        validate: 'admin/quoteitem/validate',
        view: 'admin/quoteitem/view',
        edit: 'admin/quoteitem/edit',
        print: 'admin/quoteitem/print',
        sendEmail: 'admin/quoteitem/sendEmail',
    };

$(document).ready(function() {

    // Filter the row by status ('active' field)
    $(document).on('change', `#statusFilter`, function(e) {
        // Desable default event
        e.preventDefault();
        quoteTable.ajax.reload(null, false); // Reload data
    });

    // Configuration de base pour SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    // Initialisation de DataTables
    var quoteTable = $('.'+ dtID).DataTable({
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
            { "data": "quote_number" },
            { "data": "designation" },
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
                data: "dates.quote_date",
                render: function(data, type, row) {
                    return `<div>
                        <span>Émis le: ${row.dates.quote_date}</span><br>
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
                                <a class="dropdown-item view-quote" href="#" data-id="${row.id}">
                                    <i class="fa fa-eye me-2"></i> Voir
                                </a>
                            </li>`;
                    
                    // Option Modifier (seulement si en attente)
                    if(parseInt(row.status.code) === 1) {
                        actions += `
                            <li>
                                <a class="dropdown-item edit-quote" href="#" data-id="${row.id}">
                                    <i class="fa fa-pencil me-2"></i> Modifier
                                </a>
                            </li>`;
                    }
                    
                    // Option Imprimer
                    actions += `
                            <li>
                                <a class="dropdown-item print-quote" href="#" data-id="${row.id}">
                                    <i class="fa fa-print me-2"></i> Imprimer
                                </a>
                            </li>`;
                    
                    // Option Envoyer par email
                    actions += `
                            <li>
                                <a class="dropdown-item send-quote" href="#" data-id="${row.id}">
                                    <i class="fa fa-envelope-o me-2"></i> Envoyer par email
                                </a>
                            </li>`;
                    
                    // Options Valider/Rejeter (uniquement pour les devis en attente)
                    if(parseInt(row.status.code) === 1) {
                        actions += `
                            <li>
                                <a class="dropdown-item validate-quote" href="#" data-id="${row.id}">
                                    <i class="fa fa-check me-2"></i> Valider
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item reject-quote" href="#" data-id="${row.id}">
                                    <i class="fa fa-times me-2"></i> Rejeter
                                </a>
                            </li>`;
                    }
                    
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
        quoteTable.ajax.reload(null, false);
    }


    // Voir un devis
    $('.' + dtID).on('click', '.view-quote', function() {
        var quoteId = $(this).data('id');
        window.location.href = base_url + remoteAJAXFunctions.view + '/' + quoteId;
    });

    // Modifier un devis
    $('.' + dtID).on('click', '.edit-quote', function() {
        var quoteId = $(this).data('id');
        window.location.href = base_url + remoteAJAXFunctions.edit + '/' + quoteId;
    });


    // Rejeter un devis
    $('.' + dtID).on('click', '.validate-quote', function() {
        var quoteId = $(this).data('id');
        
        Swal.fire({
            title: 'Confirmer la validation',
            text: "Voulez-vous vraiment valider ce devis ?",
            icon: 'warning',
            input: 'text',
            inputPlaceholder: 'Numéro de bon de commande',
            inputAttributes: {
                'aria-label': 'Numéro de bon de commande'
            },
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, valider',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                if (!result.value.trim()) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Veuillez saisir le numéro de bon de commande'
                    });
                    return;
                }

                // Afficher l'indicateur de chargement
                Swal.fire({
                    title: 'Validation en cours...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: base_url + remoteAJAXFunctions.validate + '/' + quoteId,
                    type: 'POST',
                    data: {
                        order_number: result.value,
                        id: quoteId
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if(response.status == "success") {
                            Toast.fire({
                                icon: 'success',
                                title: 'Devis validé avec succès'
                            });
                            refreshTable();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message || 'Erreur lors de la validation'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Toast.fire({
                            icon: 'error',
                            title: 'Erreur lors de la validation: ' + (xhr.responseJSON?.message || error)
                        });
                    }
                });
            }
        });
    });


    // Rejeter un devis
    $('.' + dtID).on('click', '.reject-quote', function() {
        var quoteId = $(this).data('id');
        
        Swal.fire({
            title: 'Confirmer le rejet',
            text: "Voulez-vous vraiment rejeter ce devis ?",
            icon: 'warning',
            input: 'textarea',
            inputPlaceholder: 'Motif du rejet',
            inputAttributes: {
                'aria-label': 'Motif du rejet'
            },
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, rejeter',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                if (!result.value.trim()) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Veuillez saisir un motif de rejet'
                    });
                    return;
                }

                // Afficher l'indicateur de chargement
                Swal.fire({
                    title: 'Rejet en cours...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: base_url + remoteAJAXFunctions.reject + '/' + quoteId,
                    type: 'POST',
                    data: {
                        reason: result.value,
                        id: quoteId
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if(response.status == "success") {
                            Toast.fire({
                                icon: 'success',
                                title: 'Devis rejeté avec succès'
                            });
                            refreshTable();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message || 'Erreur lors du rejet'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Toast.fire({
                            icon: 'error',
                            title: 'Erreur lors du rejet: ' + (xhr.responseJSON?.message || error)
                        });
                    }
                });
            }
        });
    });

    // Imprimer une facture
    $('.' + dtID).on('click', '.print-quote', function(e) {
        // console.log('print-quote');
        e.preventDefault();
        var quoteId = $(this).data('id');

        // console.log(quoteId);
        
        if(quoteId > 0) {
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
                    id: quoteId
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
                text: 'Veuillez sélectionner un devis',
                icon: 'warning'
            });
        }
    });

    // Fonction pour imprimer un devis
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
                    <title>Impression devis</title>
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
                            min-height: 297mm;
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


    
    // Confirmation avant envoi par email
    $('.' + dtID).on('click', '.send-quote', function(e) {
        e.preventDefault();
        var quoteId = $(this).data('id');
        
        Swal.fire({
            title: 'Envoi du devis par email',
            text: "Voulez-vous envoyer ce devis par email ?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, envoyer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: base_url + remoteAJAXFunctions.sendEmail + '/' + quoteId,
                    type: 'POST',
                    data: {
                        id: quoteId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if(response.status == "success") {
                            Toast.fire({
                                icon: 'success',
                                title: 'Devis envoyé avec succès'
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



});