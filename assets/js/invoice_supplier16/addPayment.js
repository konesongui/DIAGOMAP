"use strict";
// Class definition

// Set all the required variables for the following methods
var modalID = 'addPaymentModal',
    modalContentID = 'addPaymentModalContent',
    formID = 'paymentForm',
    submitID = 'paymentSubmit',
    remoteAJAXFunctions = {
        addPayment: 'admin/invoiceitem/addPaymentForm',
        setPayment: 'admin/invoiceitem/setPayment',
        print: 'admin/invoiceitem/print',
    };

$(document).ready(function() {
    // Initialisation du datepicker
    $('.datepicker').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true,
        language: 'fr'
    });

    // Function to load on (edit or add) button click
    $(document).on('click', `.add-payment`, function(e) {

        // Desable default event
        e.preventDefault();

        // Get the selected row id
        var rowID = $(this).attr('data-row-id');
        var remaining = $(this).attr('data-remaining');
        var tva_amount = $(this).attr('data-tva_amount');

        // console.log(base_url);

        // AJAX function to load the form data to display
        $.ajax({
            // AJAX Call options
            url: base_url + '/' + remoteAJAXFunctions.addPayment,
            type: "POST",
            data: {
                'rowID': rowID,
                'remaining': remaining,
                'tva_amount': tva_amount
            },
            // On 'Success' Event
            success: function(data) {

                // Process only if any data has been loaded
                if(data) {
                    // Display the loaded data
                    $(`#${modalID} #${modalContentID}`).html(data);

                    // Réinitialiser les événements après chargement du formulaire
                    initializePaymentForm();
                } // End if

            }, // End success event

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

        console.log('Tentative de soumission du paiement');

        // Validation du formulaire
        const form = $(`#${formID}`)[0];
        if (!form.checkValidity()) {
            $(form).addClass('was-validated');
            return;
        }

        // Récupérer le type de source et l'ID
        let source_type = $('#source_type').val();
        let source_id = null;
        let source_name = '';

        if (source_type === 'caisse') {
            source_id = $('#caisse_id').val();
            source_name = $('#caisse_id option:selected').text();
            if (!source_id) {
                Swal.fire({
                    title: 'Erreur',
                    text: 'Veuillez sélectionner une caisse',
                    icon: 'error'
                });
                return;
            }
        } else if (source_type === 'banque') {
            source_id = $('#banque_id').val();
            source_name = $('#banque_id option:selected').text();
            if (!source_id) {
                Swal.fire({
                    title: 'Erreur',
                    text: 'Veuillez sélectionner une banque',
                    icon: 'error'
                });
                return;
            }
        } else {
            Swal.fire({
                title: 'Erreur',
                text: 'Type de source invalide',
                icon: 'error'
            });
            return;
        }

        // Récupération des données avec source_type et source_id
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

        // Vérifier si la méthode de paiement nécessite une source
        if (['cash', 'check', 'card', 'bank', 'bank_transfer'].includes(formData.method) && !source_id) {
            Swal.fire({
                title: 'Erreur',
                text: 'Veuillez sélectionner une source de paiement',
                icon: 'error'
            });
            return;
        }

        // Configuration SweetAlert
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
                            <td style="padding: 5px;">${source_type === 'caisse' ? 'ð¦ Caisse' : 'ðï¸ Banque'}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px; font-weight: bold;">${source_type === 'caisse' ? 'Caisse' : 'Banque'}:</td>
                            <td style="padding: 5px;">${source_name}</td>
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
                                if (typeof reloadTable === 'function') {
                                    reloadTable();
                                } else {
                                    location.reload();
                                }
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
        /* Styles pour le formulaire de paiement */
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


    // Imprimer une facture
    $(document).on('click', '.print-invoice', function(e) {
        // console.log('print-invoice');
        e.preventDefault();
        var invoiceId = $(this).attr('data-row-id');

        // console.log(invoiceId);

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
                url: (typeof base_url !== 'undefined' && base_url ? base_url : (typeof baseurl !== 'undefined' ? baseurl : '/')) + remoteAJAXFunctions.print,
                data: {
                    id: invoiceId
                },
                dataType: "json",
                success: function(response) {
                    Swal.close();

                    if (!response || typeof response.page === 'undefined') {
                        Swal.fire({
                            title: 'Erreur',
                            text: 'Réponse d\'impression invalide.',
                            icon: 'error'
                        });
                        return;
                    }

                    Popup(response.page);

                    // if (response.status === "success") {
                    //     Popup(response.page);
                    // } else {
                    //     Swal.fire({
                    //         title: 'Erreur',
                    //         text: response.message || 'Erreur lors de l\'impression',
                    //         icon: 'error'
                    //     });
                    // }
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
            var html = (typeof data === 'string') ? data : '';
            if (html.length >= 2 && html.charCodeAt(0) === 96 && html.charCodeAt(html.length - 1) === 96) {
                html = html.slice(1, -1);
            }
            var trimmed = html.replace(/^\uFEFF/, '').trim();
            var isFullDoc = /^<!DOCTYPE/i.test(trimmed) || /^<html[\s>]/i.test(trimmed);
            if (isFullDoc) {
                frameDoc.document.write(trimmed);
            } else {
                var printPrefix = '<!DOCTYPE html><html><head><title>Impression Facture</title><meta charset="utf-8">' +
                    '<style>body{margin:0;padding:0;background-color:white;font-family:Arial,sans-serif;line-height:1.4;color:#000;}' +
                    '@page{size:A4;margin:15mm 10mm;}' +
                    '*{-webkit-print-color-adjust:exact!important;color-adjust:exact!important;print-color-adjust:exact!important;}' +
                    '.page-a4{width:210mm;margin:0 auto;padding:15mm 10mm;box-sizing:border-box;position:relative;background:white;box-shadow:0 0 5px rgba(0,0,0,0.1);}' +
                    'table,img,div{page-break-inside:avoid;}.page-break{page-break-after:always;}.no-print{display:none!important;}' +
                    '@media print{body,.page-a4{background:white;width:auto;height:auto;margin:0;padding:0;box-shadow:none;}' +
                    '.no-print,.no-print *{display:none!important;}.print-only{display:block!important;}}</style></head><body><div class="page-a4">';
                var printSuffix = '</div><script>window.onload=function(){setTimeout(function(){window.focus();window.print();},500);};\x3c/script></body></html>';
                frameDoc.document.write(printPrefix);
                frameDoc.document.write(html);
                frameDoc.document.write(printSuffix);
            }
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