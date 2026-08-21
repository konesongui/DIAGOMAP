"use strict";
// Class definition

// Set all the required variables for the following methods
var dtID = 'quotesDatatable',
    remoteAJAXFunctions = {
        loadData: 'admin/selling/data',
        reject: 'admin/selling/reject',
        validate: 'admin/selling/validate',
        view: 'admin/selling/view_selling',
        delete: 'admin/selling/delete',
        edit: 'admin/selling/edit',
        print: 'admin/selling/print_selling',
        sendEmail: 'admin/selling/sendEmail',
        duplicate: 'admin/selling/duplicate',
        setFilterMode: 'admin/selling/setFilterMode',
        getFilterMode: 'admin/selling/getFilterMode'
    };

$(document).ready(function() {
    // Configuration de base pour SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    // Récupérer la préférence de filtre au chargement
    let currentFilterMode = 'all';

    // Fonction pour charger la préférence de filtre
    function loadFilterPreference() {
        $.ajax({
            url: baseurl + remoteAJAXFunctions.getFilterMode,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.filter_mode) {
                    currentFilterMode = response.filter_mode;
                    // Mettre à jour l'interface
                    updateFilterUI(currentFilterMode);
                    // Recharger le tableau avec le filtre
                    if (quoteTable) {
                        quoteTable.ajax.reload(null, false);
                    }
                }
            }
        });
    }

    // Fonction pour mettre à jour l'interface du filtre
    function updateFilterUI(filterMode) {
        $('.filter-toggle').removeClass('btn-primary').addClass('btn-default');
        $(`.filter-toggle[data-filter="${filterMode}"]`).removeClass('btn-default').addClass('btn-primary');

        // Mettre à jour le badge
        var badgeHtml = filterMode == 'all'
            ? '<i class="fa fa-eye"></i> Vue administrateur (factures)'
            : '<i class="fa fa-user"></i> Mes factures uniquement';

        // Ajouter les badges de rôle si présents
        if ($('.badge small').length > 0) {
            var roleText = $('.badge small').first().text();
            badgeHtml += ' <small>' + roleText + '</small>';
        }

        $('.badge').html(badgeHtml);
        $('.badge').removeClass('bg-success bg-info')
            .addClass(filterMode == 'all' ? 'bg-success' : 'bg-info');
    }

    // Gestionnaire pour le filtre toggle
    $(document).on('click', '.filter-toggle', function(e) {
        e.preventDefault();
        var filterMode = $(this).data('filter');

        // Sauvegarder la préférence
        $.ajax({
            url: baseurl + remoteAJAXFunctions.setFilterMode,
            type: 'POST',
            data: { filter_mode: filterMode },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    currentFilterMode = response.filter_mode;
                    updateFilterUI(currentFilterMode);
                    // Recharger le tableau
                    if (quoteTable) {
                        quoteTable.ajax.reload(null, false);
                    }
                }
            }
        });
    });

    // Filter the row by status
    $(document).on('change', '#statusFilter', function(e) {
        e.preventDefault();
        if (quoteTable) {
            quoteTable.ajax.reload(null, false);
        }
    });

    // Initialisation de DataTables
    var quoteTable = $('.' + dtID).DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": baseurl + remoteAJAXFunctions.loadData,
            "type": "POST",
            "data": function(d) {
                d.status = $("#statusFilter").val();
                d.filter_mode = currentFilterMode;
            },
        },
        "columns": [
            { "data": "quote_number", "title": "N° Devis" },

            {
                data: "dates.quote_date",
                title: "Dates",
                render: function(data, type, row) {
                    return `<div>
                        <span>Émis le: ${row.dates.quote_date}</span><br>
                        <small>Valide: ${row.dates.valid_until}</small>
                    </div>`;
                }
            },

            {
                "data": "dates.creation",
                "title": "Création",
                render: function(data) {
                    return `<div>
                        <strong>${data}</strong><br>
                    </div>`;
                }
            },

            {
                data: "amount",
                title: "Montants",
                render: function(data) {
                    return `<div>
                        <strong>TTC: ${data.ttc}</strong><br>
                        <small>HT: ${data.ht}</small>
                    </div>`;
                }
            },

            // 🔹 NOUVEAU : Montant payé
            {
                data: "payment",
                title: "💰 Montant payé",
                render: function(data, type, row) {
                    if (data && parseFloat(data.amount_paid_raw) > 0) {
                        return `<span class="text-success"><strong>${data.amount_paid} FCFA</strong></span>`;
                    }
                    return '<span class="text-muted">0,00 FCFA</span>';
                }
            },

            // 🔹 NOUVEAU : Montant rendu
            {
                data: "payment",
                title: "🔄 Montant rendu",
                render: function(data, type, row) {
                    if (data && parseFloat(data.change_amount_raw) > 0) {
                        return `<span class="text-warning"><strong>${data.change_amount} FCFA</strong></span>`;
                    }
                    return '<span class="text-muted">0,00 FCFA</span>';
                }
            },

            // 🔹 NOUVEAU : Reste à payer
            {
                data: "payment",
                title: "⚠️ Reste à payer",
                render: function(data, type, row) {
                    if (data && parseFloat(data.remaining_amount_raw) > 0) {
                        return `<span class="text-danger"><strong>${data.remaining_amount} FCFA</strong></span>`;
                    }
                    return '<span class="text-success">0,00 FCFA</span>';
                }
            },

            // 🔹 NOUVEAU : Statut du paiement
            {
                data: "payment",
                title: "📊 Statut paiement",
                render: function(data, type, row) {
                    if (data && data.payment_status_label) {
                        return `<span class="label ${data.payment_status_class}">${data.payment_status_label}</span>`;
                    }
                    return '<span class="label label-default">⏳ En attente</span>';
                }
            },

            {
                data: "status",
                title: "Statut facture",
                render: function(data) {
                    return `<span class="label ${data.class}">${data.label}</span>`;
                }
            },

            {
                "data": "user_name",
                "title": "Utilisateur"
            },

            {
                data: null,
                title: "Actions",
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

                    // Option Dupliquer (pour tous sauf brouillon)
                    if(parseInt(row.status.code) !== 0) {
                        actions += `
                            <li>
                                <a class="dropdown-item duplicate-quote" href="#" data-id="${row.id}" data-reference="${row.quote_number}">
                                    <i class="fa fa-copy me-2"></i> Dupliquer
                                </a>
                            </li>`;
                    }

                    // Option Modifier (seulement si en attente)
                    if(parseInt(row.status.code) === 1) {
                        actions += `
                            <li>
                                <a class="dropdown-item edit-quote" href="#" data-id="${row.id}" data-status="${row.status.code}">
                                    <i class="fa fa-pencil me-2"></i> Modifier
                                </a>
                            </li>`;
                    }

                    // Option Supprimer (seulement si en attente)
                    if(parseInt(row.status.code) === 1) {
                        actions += `
                            <li>
                                <a class="dropdown-item delete-quote" href="#" data-id="${row.id}">
                                    <i class="fa fa-trash me-2"></i> Supprimer
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
                    if (row.customer && row.customer.email) {
                        actions += `
                            <li>
                                <a class="dropdown-item send-quote" href="#" data-id="${row.id}">
                                    <i class="fa fa-envelope-o me-2"></i> Envoyer par email
                                </a>
                            </li>`;
                    }

                    // Options Valider/Rejeter (uniquement pour les devis en attente)
                    if(parseInt(row.status.code) === 1) {
                        actions += `
                            <li><hr class="dropdown-divider"></li>
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
        "order": [[0, "desc"]],
        "language": {
            "url": baseurl + "assets/js/french.json"
        },
        "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "pageLength": 25,
        "initComplete": function() {
            // Charger la préférence de filtre après l'initialisation
            loadFilterPreference();
        }
    });

    // Fonction pour rafraîchir la table
    function refreshTable() {
        if (quoteTable) {
            quoteTable.ajax.reload(null, false);
        }
    }

    // Voir un devis
    $('.' + dtID).on('click', '.view-quote', function(e) {
        e.preventDefault();
        var quoteId = $(this).data('id');
        window.location.href = base_url + remoteAJAXFunctions.view + '/' + quoteId;
    });

    // Supprimer un devis - AVEC CONFIRMATION
    $('.' + dtID).on('click', '.delete-quote', function(e) {
        e.preventDefault();
        var quoteId = $(this).data('id');

        Swal.fire({
            title: 'Confirmer la suppression',
            text: "Voulez-vous vraiment supprimer ce devis ? Cette action est irréversible.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Suppression en cours...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: base_url + remoteAJAXFunctions.delete + '/' + quoteId,
                    type: 'POST',
                    data: {
                        id: quoteId
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if(response.status == "success") {
                            Toast.fire({
                                icon: 'success',
                                title: 'Devis supprimé avec succès'
                            });
                            refreshTable();
                        } else {
                            Toast.fire({
                                icon: 'error',
                                title: response.message || 'Erreur lors de la suppression'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        Toast.fire({
                            icon: 'error',
                            title: 'Erreur lors de la suppression: ' + (xhr.responseJSON?.message || error)
                        });
                    }
                });
            }
        });
    });

    // Modifier un devis
    $('.' + dtID).on('click', '.edit-quote', function(e) {
        e.preventDefault();
        const quoteId = $(this).data('id');
        const statusCode = $(this).data('status');

        if (parseInt(statusCode) === 1) {
            Swal.fire({
                title: 'Attention',
                text: "Ce devis est en attente de validation. Voulez-vous vraiment le modifier ?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, modifier',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = base_url + remoteAJAXFunctions.edit + '/' + quoteId;
                }
            });
        } else {
            window.location.href = base_url + remoteAJAXFunctions.edit + '/' + quoteId;
        }
    });

    // Dupliquer un devis
    $('.' + dtID).on('click', '.duplicate-quote', function(e) {
        e.preventDefault();
        const quoteId = $(this).data('id');
        const reference = $(this).data('reference');

        Swal.fire({
            title: 'Dupliquer le devis',
            html: `Voulez-vous créer une copie du devis <strong>${reference}</strong> ?<br>
              <small>Un nouveau devis sera créé avec les mêmes informations.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, dupliquer',
            cancelButtonText: 'Annuler',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return $.ajax({
                    url: base_url + remoteAJAXFunctions.duplicate + '/' + quoteId,
                    type: 'POST',
                    dataType: 'json'
                }).then(response => {
                    if (!response.success) {
                        throw new Error(response.message || 'Erreur lors de la duplication');
                    }
                    return response;
                }).catch(error => {
                    Swal.showValidationMessage(`Erreur: ${error.message}`);
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const response = result.value;

                Swal.fire({
                    title: 'Succès !',
                    html: `Devis dupliqué avec succès.<br>
                      <small>Nouvelle référence: <strong>${response.new_reference}</strong></small>`,
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Modifier le nouveau devis',
                    cancelButtonText: 'Rester sur la liste'
                }).then((editResult) => {
                    if (editResult.isConfirmed) {
                        window.location.href = base_url + 'admin/selling/edit/' + response.new_quote_id;
                    } else {
                        refreshTable();
                    }
                });
            }
        });
    });

    // Valider un devis
    $('.' + dtID).on('click', '.validate-quote', function(e) {
        e.preventDefault();
        var quoteId = $(this).data('id');

        Swal.fire({
            title: 'Confirmer la validation',
            text: "Voulez-vous vraiment valider ce devis ?",
            icon: 'question',
            input: 'text',
            inputPlaceholder: 'Numéro de bon de commande',
            inputAttributes: {
                'aria-label': 'Numéro de bon de commande',
                'required': 'required'
            },
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, valider',
            cancelButtonText: 'Annuler',
            preConfirm: (orderNumber) => {
                if (!orderNumber || !orderNumber.trim()) {
                    Swal.showValidationMessage('Veuillez saisir le numéro de bon de commande');
                    return false;
                }
                return orderNumber;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Validation en cours...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: base_url + remoteAJAXFunctions.validate,
                    type: 'POST',
                    data: {
                        id: quoteId,
                        order_number: result.value
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
                    error: function(xhr) {
                        Swal.close();
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON?.message || 'Erreur lors de la validation'
                        });
                    }
                });
            }
        });
    });

    // Rejeter un devis
    $('.' + dtID).on('click', '.reject-quote', function(e) {
        e.preventDefault();
        var quoteId = $(this).data('id');

        Swal.fire({
            title: 'Confirmer le rejet',
            text: "Voulez-vous vraiment rejeter ce devis ?",
            icon: 'warning',
            input: 'textarea',
            inputPlaceholder: 'Motif du rejet',
            inputAttributes: {
                'aria-label': 'Motif du rejet',
                'required': 'required'
            },
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, rejeter',
            cancelButtonText: 'Annuler',
            preConfirm: (reason) => {
                if (!reason || !reason.trim()) {
                    Swal.showValidationMessage('Veuillez saisir un motif de rejet');
                    return false;
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Rejet en cours...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: base_url + remoteAJAXFunctions.reject,
                    type: 'POST',
                    data: {
                        id: quoteId,
                        reason: result.value
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
                    error: function(xhr) {
                        Swal.close();
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON?.message || 'Erreur lors du rejet'
                        });
                    }
                });
            }
        });
    });

    // Imprimer un devis
    $('.' + dtID).on('click', '.print-quote', function(e) {
        e.preventDefault();
        var quoteId = $(this).data('id');

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
                    if (response.status === 'error') {
                        Swal.fire({
                            title: 'Erreur',
                            text: response.message,
                            icon: 'error'
                        });
                    } else {
                        Popup(response.page);
                    }
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

    // Envoyer par email
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
                Swal.fire({
                    title: 'Envoi en cours...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: base_url + remoteAJAXFunctions.sendEmail,
                    type: 'POST',
                    data: {
                        id: quoteId
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
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
                    error: function(xhr) {
                        Swal.close();
                        Toast.fire({
                            icon: 'error',
                            title: xhr.responseJSON?.message || 'Erreur lors de l\'envoi'
                        });
                    }
                });
            }
        });
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
                        body {
                            margin: 0;
                            padding: 0;
                            background-color: white;
                            font-family: Arial, sans-serif;
                            line-height: 1.4;
                            color: #000;
                        }
                        
                        @page {
                            size: A4;
                            margin: 15mm 10mm;
                        }
                        
                        * {
                            -webkit-print-color-adjust: exact !important;
                            color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }
                        
                        .page-a4 {
                            width: 210mm;
                            margin: 0 auto;
                            padding: 15mm 10mm;
                            box-sizing: border-box;
                            position: relative;
                            background: white;
                        }
                        
                        table, img, div {
                            page-break-inside: avoid;
                        }
                        
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
                            
                            .no-print, .no-print * {
                                display: none !important;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="page-a4">
                        ${data}
                    </div>
                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.focus();
                                window.print();
                            }, 500);
                        };
                    <\/script>
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
                } finally {
                    setTimeout(function() {
                        frame1.remove();
                    }, 1000);
                }
            }, 1000);

            return true;
        } catch (e) {
            console.error("Erreur lors de la création de la fenêtre d'impression:", e);
            return false;
        }

    }
});