"use strict";
// Class definition

// Set all the required variables for the following methods
var dtID = 'invoiceDatatable',
    modalID = 'addPaymentModal',
    modalContentID = 'addPaymentContent',
    formID = 'paymentForm',
    submitID = 'paymentSubmit',
    remoteAJAXFunctions = {
        loadData: 'admin/invoiceitem/data',
        view: 'admin/invoiceitem/view',
        edit: 'admin/invoiceitem/edit',
        print: 'admin/invoiceitem/print',
        sendEmail: 'admin/invoiceitem/sendEmail',
        addPayment: 'admin/invoiceitem/addPaymentForm',
        setPayment: 'admin/invoiceitem/setPayment',
        cancel: 'admin/invoiceitem/cancel',
        certifyFNE: 'admin/invoiceitem/certifyFNE',
        getFNEStatus: 'admin/invoiceitem/getFNEStatus'
    };

$(document).ready(function() {
    // Filter the row by status ('active' field)
    $(document).on('change', `#statusFilter`, function(e) {
        // Desable default event
        e.preventDefault();
        invoiceTable.ajax.reload(null, false); // Reload data
    });

    // Configuration de base pour SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });

    // Initialisation de DataTables
    var invoiceTable = $('.'+ dtID).DataTable({
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
            { "data": "invoice_number" },
            {
                data: "customer",
                render: function(data) {
                    return `<div>
                        <strong>${data.name}</strong><br>
                        <small>${data.email}<br>${data.phone}</small>
                    </div>`;
                }
            },
            { "data": "dates.invoice" },
            { "data": "dates.due" },
            {
                "data": "amount.ht",
                "render": function(data) {
                    return data;
                }
            },
            {
                "data": "amount.tva_amount",
                "render": function(data) {
                    return data;
                }
            },
            {
                "data": "amount.ttc",
                "render": function(data) {
                    return data;
                }
            },
            {
                "data": "amount.paid",
                "render": function(data) {
                    return data;
                }
            },
            {
                "data": "amount.remaining",
                "render": function(data) {
                    return data;
                }
            },
            { "data": "user_name" },
            {
                data: "fne_status",
                render: function(data) {
                    if(data && data.certified) {
                        return `<span class="label label-success" title="Réf FNE: ${data.reference}">
                            <i class="fa fa-check-circle"></i> FNE
                        </span>`;
                    } else {
                        return `<span class="label label-warning">
                            <i class="fa fa-clock-o"></i> Non FNE
                        </span>`;
                    }
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
                                <a class="dropdown-item" href="${baseurl}admin/invoiceitem/view/${row.id}">
                                    <i class="fa fa-eye me-2"></i> Voir
                                </a>
                            </li>`;

                    // Option Envoyer par email
                    actions += `
                     <li>
                         <a class="dropdown-item send-invoice" href="#" data-id="${row.id}">
                             <i class="fa fa-envelope-o me-2"></i> Envoyer par email
                         </a>
                     </li>`;

                    // Option Certifier FNE (si non certifiée)
                    if (!row.fne_status || !row.fne_status.certified) {
                        actions += `
                            <li>
                                <a class="dropdown-item certify-fne" href="#" data-id="${row.id}">
                                    <i class="fa fa-check-circle me-2"></i> Certifier FNE
                                </a>
                            </li>`;
                    }

                    // Option Payer (si non payée et montant restant > 0)
                    if ((parseInt(row.status.code) == 1 || parseInt(row.status.code) == 3) && parseFloat(row.amount.remaining) > 0) {
                        actions += `
                            <li>
                                <a data-toggle="modal" data-target="#${modalID}" data-row-id="${row.id}" data-toggle="tooltip" data-placement="left" title="Ajouter un paiement" type="button" data-remaining="${row.amount.remaining}" class="dropdown-item add-payment">
                                    <i class="fa fa-money me-2"></i> Ajouter un paiement
                                </a>  
                            </li>`;
                    }

                    // Option Annuler (si non payée)
                    if (parseInt(row.status.code) == 1) {
                        actions += `
                            <li>
                                <a class="dropdown-item" href="${baseurl}admin/invoiceitem/edit/${row.id}">
                                    <i class="fa fa-edit me-2"></i> Modifier
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item cancel-invoice" href="#" data-id="${row.id}">
                                    <i class="fa fa-ban me-2"></i> Annuler
                                </a>
                            </li>`;
                    }

                    // Option Imprimer
                    actions += `
                            <li>
                                <a class="dropdown-item print-invoice" href="#" data-id="${row.id}">
                                    <i class="fa fa-print me-2"></i> Imprimer
                                </a>
                            </li>`;

                    actions += `
                        </ul>
                    </div>`;

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
        invoiceTable.ajax.reload(null, false);
    }

    // Certification FNE
    $('.' + dtID).on('click', '.certify-fne', function(e) {
        e.preventDefault();
        var invoiceId = $(this).data('id');

        Swal.fire({
            title: 'Certification FNE',
            text: "Voulez-vous certifier cette facture auprès de la FNE ?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, certifier',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Certification en cours...',
                    html: 'Connexion à la plateforme FNE...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: base_url + '/' + remoteAJAXFunctions.certifyFNE,
                    type: 'POST',
                    data: {
                        id: invoiceId
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();

                        if(response.status === "success") {
                            Swal.fire({
                                title: 'Succès FNE',
                                html: `
                                    <div class="text-left">
                                        <p><strong>Facture certifiée avec succès !</strong></p>
                                        <p><strong>Référence FNE:</strong> ${response.data.reference}</p>
                                        <p><strong>Token de vérification:</strong> ${response.data.token}</p>
                                        <p><strong>Stickers restants:</strong> ${response.data.balance_sticker}</p>
                                        <p class="text-success"><i class="fa fa-check"></i> Cette facture est maintenant conforme à la réglementation FNE</p>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: 'Fermer'
                            });
                            refreshTable();
                        } else {
                            Swal.fire({
                                title: 'Erreur FNE',
                                html: `
                                    <div class="text-left">
                                        <p><strong>Erreur lors de la certification:</strong></p>
                                        <p>${response.message || 'Erreur inconnue'}</p>
                                        ${response.details ? `<p><small>Détails: ${response.details}</small></p>` : ''}
                                    </div>
                                `,
                                icon: 'error',
                                confirmButtonText: 'Compris'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            title: 'Erreur Serveur',
                            text: xhr.responseJSON?.message || 'Erreur de connexion avec la plateforme FNE',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });

    // Fonction de certification automatique après paiement complet
    function certifyInvoiceFNE(invoiceId) {
        $.ajax({
            url: base_url + '/' + remoteAJAXFunctions.certifyFNE,
            type: 'POST',
            data: { id: invoiceId },
            dataType: 'json',
            success: function(response) {
                if(response.status === "success") {
                    console.log('Certification FNE automatique réussie:', response.data.reference);
                }
            }
        });
    }

    // Voir une facture
    $('.' + dtID).on('click', '.view-invoice', function() {
        var invoiceId = $(this).data('id');
        window.location.href = base_url + '/'+ remoteAJAXFunctions.view  + '/'+ invoiceId;
    });

    // Modifier une facture
    $('.' + dtID).on('click', '.edit-invoice', function() {
        var invoiceId = $(this).data('id');
        window.location.href = base_url + '/'+ remoteAJAXFunctions.edit + '/'+ invoiceId;
    });

    // Imprimer une facture
    $('.' + dtID).on('click', '.print-invoice', function(e) {
        e.preventDefault();
        var invoiceId = $(this).data('id');

        if(invoiceId > 0) {
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
                    id: invoiceId
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
                text: 'Veuillez sélectionner une facture',
                icon: 'warning'
            });
        }
    });

    // Fonction pour imprimer une facture
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
                    <title>Impression Facture</title>
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
                            box-shadow: 0 0 5px rgba(0,0,0,0.1);
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
    $('.' + dtID).on('click', '.send-invoice', function(e) {
        e.preventDefault();
        var invoiceId = $(this).data('id');

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
                    url: base_url + '/'+ remoteAJAXFunctions.sendEmail,
                    type: 'POST',
                    data: {
                        id: invoiceId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if(response.status == "success") {
                            Toast.fire({
                                icon: 'success',
                                title: 'Facture envoyée avec succès'
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

    // Annulation de livraison
    $('.' + dtID).on('click', '.cancel-invoice', function() {
        var invoiceId = $(this).data('id');

        Swal.fire({
            title: 'Confirmer l\'annulation',
            text: "Voulez-vous vraiment annuler cette facture ?",
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
                    url: base_url + '/' + remoteAJAXFunctions.cancel,
                    type: 'POST',
                    data: {
                        reason: result.value,
                        id: invoiceId
                    },
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if(response.status == "success") {
                            Toast.fire({
                                icon: 'success',
                                title: 'Facture annulée avec succès'
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

    // Function to load on (edit or add) button click
    $('.' + dtID).on('click', `.add-payment`, function(e) {
        e.preventDefault();
        var rowID = $(this).attr('data-row-id');
        var remaining = $(this).attr('data-remaining');
        var tva_amount = $(this).attr('data-tva_amount');

        $.ajax({
            url: base_url + '/' + remoteAJAXFunctions.addPayment,
            type: "POST",
            data: {
                'rowID': rowID,
                'remaining': remaining,
                'tva_amount': tva_amount
            },
            success: function(data) {
                if(data) {
                    $(`#${modalID} #${modalContentID}`).html(data);
                    $('.date').datepicker({
                        format: 'dd/mm/yyyy',
                        autoclose: true,
                        todayHighlight: true
                    });

                    // Initialiser les événements du formulaire de paiement
                    initializePaymentForm();
                }
            }
        });
    });

    // Fonction pour initialiser les événements du formulaire de paiement
    function initializePaymentForm() {

        // Initialiser le datepicker dans le modal
        $('#payment_date').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            language: 'fr'
        }).datepicker('setDate', new Date());

        // Gérer la sélection de la méthode de paiement
        $('#method').off('change').on('change', function() {
            let m = $(this).val();

            // Masquer/afficher les champs appropriés
            if (['cash','check','card'].includes(m)) {
                $('#caisse_group').show();
                $('#banque_group').hide();
                $('#caisse_id').prop('required', true);
                $('#banque_id').prop('required', false);
                $('#source_type').val('caisse');

                // Sélectionner automatiquement la première option si disponible
                if ($('#caisse_id option').length > 1) {
                    $('#caisse_id').val($('#caisse_id option:eq(1)').val()).trigger('change');
                }
            } else if (['bank','bank_transfer'].includes(m)) {
                $('#caisse_group').hide();
                $('#banque_group').show();
                $('#caisse_id').prop('required', false);
                $('#banque_id').prop('required', true);
                $('#source_type').val('banque');

                // Sélectionner automatiquement la première option si disponible
                if ($('#banque_id option').length > 1) {
                    $('#banque_id').val($('#banque_id option:eq(1)').val()).trigger('change');
                }
            } else {
                $('#caisse_group, #banque_group').hide();
                $('#caisse_id').prop('required', false);
                $('#banque_id').prop('required', false);
            }

            // Gérer le champ référence
            if (m === 'cash') {
                $('#reference').closest('.form-group').fadeOut();
            } else {
                $('#reference').closest('.form-group').fadeIn();
            }

            // Mettre à jour l'info de source
            updateSourceInfo();
        });

        // Fonction pour mettre à jour l'information de la source sélectionnée
        function updateSourceInfo() {
            let method = $('#method').val();
            let sourceText = '';

            if (method === 'cash' || method === 'check' || method === 'card') {
                let selectedCaisse = $('#caisse_id option:selected').text();
                if ($('#caisse_id').val()) {
                    sourceText = 'Source : ' + selectedCaisse;
                    $('#source_info').fadeIn();
                } else {
                    $('#source_info').fadeOut();
                }
            } else if (method === 'bank_transfer' || method === 'bank') {
                let selectedBanque = $('#banque_id option:selected').text();
                if ($('#banque_id').val()) {
                    sourceText = 'Source : ' + selectedBanque;
                    $('#source_info').fadeIn();
                } else {
                    $('#source_info').fadeOut();
                }
            } else {
                $('#source_info').fadeOut();
            }

            $('#selected_source_info').text(sourceText);
        }

        // Écouter les changements sur les sélecteurs
        $('#caisse_id, #banque_id').off('change').on('change', updateSourceInfo);

        // Initialiser l'affichage
        $('#method').trigger('change');
    }

    // Soumission du formulaire avec confirmation
    $(document).on('click', `#${submitID}`, function(e) {
        e.preventDefault();

        const form = $(`#${formID}`)[0];
        if (!form.checkValidity()) {
            $(form).addClass('was-validated');
            return;
        }

        // Récupérer la source de paiement
        let source_type = $('#source_type').val();
        let source_id = null;
        let source_label = '';

        if (source_type === 'caisse') {
            source_id = $('#caisse_id').val();
            source_label = $('#caisse_id option:selected').text();
        } else if (source_type === 'banque') {
            source_id = $('#banque_id').val();
            source_label = $('#banque_id option:selected').text();
        }

        // Validation de la source
        if (!source_id) {
            Swal.fire({
                title: 'Erreur de validation',
                text: 'Veuillez sélectionner une source de paiement',
                icon: 'error'
            });
            return;
        }

        const formData = {
            invoice_id: $('#payment_invoice_id').val(),
            amount: $('#amount').val(),
            payment_date: $('#payment_date').val(),
            method: $('#method').val(),
            reference: $('#reference').val(),
            notes: $('#notes').val(),
            source_type: source_type,
            source_id: source_id
        };

        // Afficher les détails dans la confirmation
        Swal.fire({
            title: 'Confirmer le paiement',
            html: `
                <p>Voulez-vous enregistrer ce paiement ?</p>
                <div class="text-left" style="text-align: left; margin-top: 15px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">Montant:</td>
                            <td style="padding: 5px;">${parseFloat(formData.amount).toLocaleString('fr-FR')} FCFA</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">Date:</td>
                            <td style="padding: 5px;">${formData.payment_date}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">Méthode:</td>
                            <td style="padding: 5px;">${formData.method}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">Source:</td>
                            <td style="padding: 5px;">${source_type === 'caisse' ? '🏦 Caisse' : '🏛️ Banque'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">${source_type === 'caisse' ? 'Caisse' : 'Banque'}:</td>
                            <td style="padding: 5px;">${source_label}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">Référence:</td>
                            <td style="padding: 5px;">${formData.reference || 'Non spécifiée'}</td>
                        </tr>
                    </table>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, enregistrer',
            cancelButtonText: 'Annuler',
            customClass: {
                container: 'sweetalert-container'
            },
            didOpen: () => {
                $('.sweetalert-container').css('z-index', '999999');
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();

                $.ajax({
                    url: `${base_url}/${remoteAJAXFunctions.setPayment}`,
                    type: 'POST',
                    data: formData,
                    dataType: 'json'
                })
                    .done(function(response) {
                        if(response.status === "success") {
                            // Certification FNE automatique si paiement complet
                            if(response.data && (response.data.remaining === 0 || response.data.remaining === '0.00')) {
                                certifyInvoiceFNE(response.data.invoice_id);
                            }

                            Swal.fire({
                                title: 'Succès',
                                text: response.message || 'Paiement enregistré avec succès',
                                icon: 'success',
                                customClass: {
                                    container: 'sweetalert-container'
                                }
                            }).then(() => {
                                // Fermer le modal
                                $(`#${modalID}`).modal('hide');
                                // Recharger la page ou mettre à jour les données
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Erreur',
                                text: response.message || 'Erreur lors du traitement',
                                icon: 'error'
                            });
                        }
                    })
                    .fail(function(xhr) {
                        console.error('Erreur AJAX:', xhr.responseText);
                        let errorMsg = 'Erreur serveur';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMsg = response.message || errorMsg;
                        } catch(e) {
                            console.error('Erreur parsing JSON:', e);
                        }
                        Swal.fire({
                            title: 'Erreur',
                            text: errorMsg,
                            icon: 'error'
                        });
                    })
                    .always(() => Swal.hideLoading());
            }
        });
    });

    // Gestion de la validation Bootstrap
    $(`#${formID} input, #${formID} select`).on('change keyup', function() {
        $(`#${formID}`).removeClass('was-validated');
    });

    // CSS à ajouter pour garantir l'affichage correct
    const style = document.createElement('style');
    style.textContent = `
        .sweetalert-container {
            z-index: 999999 !important;
        }
        .sweetalert-popup {
            z-index: 999999 !important;
        }
        .label-success {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .label-warning {
            background-color: #ffc107;
            color: #212529;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        #caisse_group, #banque_group {
            transition: all 0.3s ease;
        }
        #source_info {
            margin-top: 10px;
        }
        #source_info .alert {
            padding: 10px;
            border-radius: 4px;
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        .text-left {
            text-align: left;
        }
    `;
    document.head.appendChild(style);
});