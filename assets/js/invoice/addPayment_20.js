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
                } // End if

            }, // End success event

        });

    });



    // Soumission du formulaire avec confirmation
    $(document).on('click', `#${submitID}`, function(e) {
        e.preventDefault();

        console.log('test');
        
        // Validation du formulaire
        const form = $(`#${formID}`)[0];
        if (!form.checkValidity()) {
            $(form).addClass('was-validated');
            return;
        }

        // Récupération des données
        const formData = {
            invoice_id: $('#payment_invoice_id').val(),
            amount: $('#amount').val(),
            payment_date: $('#payment_date').val(),
            method: $('#method').val(),
            reference: $('#reference').val(),
            notes: $('#notes').val()
        };

        // Configuration SweetAlert
        Swal.fire({
            title: 'Confirmer le paiement',
            html: `
                <p>Voulez-vous enregistrer ce paiement ?</p>
                <div class="text-left">
                    <strong>Montant:</strong> ${formData.amount}<br>
                    <strong>Date:</strong> ${formData.payment_date}<br>
                    <strong>Méthode:</strong> ${formData.method}
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, envoyer',
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
                            text: 'Paiement enregistré avec succès',
                            icon: 'success',
                            customClass: {
                                container: 'sweetalert-container'
                            }
                        }).then(() => {
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
                    Swal.fire({
                        title: 'Erreur',
                        text: xhr.responseJSON?.message || 'Erreur serveur',
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
                url: base_url + remoteAJAXFunctions.print,
                data: {
                    id: invoiceId
                },
                dataType: "JSON",
                success: function(response) {
                    Swal.close();

                    // console.log(response);

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
            frameDoc.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Impression Facture</title>
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