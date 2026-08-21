"use strict";

class InvoiceFNEIntegration {
    constructor() {
        this.baseURL = base_url;
        this.remoteAJAXFunctions = {
            loadData: 'admin/invoice/data',
            certifyFNE: 'admin/FNE_invoice_controller/certifyInvoice',
            checkFNEStatus: 'admin/FNE_invoice_controller/checkInvoiceCertificationStatus',
            certifyRefund: 'admin/FNE_invoice_controller/certifyRefund'
        };
        this.initEvents();
    }

    initEvents() {
        // Certifier une facture FNE
        $(document).on('click', '.certify-fne-invoice', (e) => this.certifyInvoice(e));

        // Vérifier le statut FNE
        $(document).on('click', '.check-fne-invoice-status', (e) => this.checkStatus(e));

        // Certifier un avoir FNE
        $(document).on('click', '.certify-fne-refund', (e) => this.certifyRefund(e));
    }

    certifyInvoice(e) {
        e.preventDefault();
        const invoiceId = $(e.currentTarget).data('id');

        Swal.fire({
            title: 'Certification FNE',
            html: `
                <div class="text-start">
                    <p>Voulez-vous certifier cette facture auprès de la plateforme FNE ?</p>
                    <div class="alert alert-info mt-2">
                        <small>
                            <i class="fa fa-info-circle"></i> 
                            Cette action enverra la facture à la plateforme FNE pour certification électronique.
                            <br><strong>Prérequis :</strong> La facture doit être payée.
                        </small>
                    </div>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, certifier',
            cancelButtonText: 'Annuler',
            width: '500px'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submitInvoiceCertification(invoiceId);
            }
        });
    }

    submitInvoiceCertification(invoiceId) {
        Swal.fire({
            title: 'Certification en cours...',
            html: `
                <div class="text-center">
                    <p>Connexion à la plateforme FNE</p>
                    <div class="spinner-border text-primary mt-2" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: this.baseURL + this.remoteAJAXFunctions.certifyFNE + '/' + invoiceId,
            type: 'POST',
            dataType: 'json',
            success: (response) => {
                Swal.close();
                if(response.status === "success") {
                    this.showInvoiceCertificationSuccess(response);
                    this.refreshInvoiceTable();
                } else {
                    this.showError(response.message);
                }
            },
            error: (xhr, status, error) => {
                Swal.close();
                let errorMessage = 'Erreur lors de la certification';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                this.showError(errorMessage);
            }
        });
    }

    showInvoiceCertificationSuccess(response) {
        Swal.fire({
            title: 'Certification réussie !',
            html: `
                <div class="text-start">
                    <p><strong>Référence FNE :</strong> ${response.fne_data?.reference || 'N/A'}</p>
                    <p><strong>Token :</strong> <small>${response.fne_data?.token || 'N/A'}</small></p>
                    <p><strong>Solde stickers :</strong> ${response.fne_data?.balance_sticker || 'N/A'}</p>
                    <div class="alert alert-success mt-3">
                        <i class="fa fa-check-circle"></i> 
                        ${response.message}
                    </div>
                </div>
            `,
            icon: 'success',
            confirmButtonText: 'Fermer'
        });
    }

    checkStatus(e) {
        e.preventDefault();
        const invoiceId = $(e.currentTarget).data('id');

        Swal.fire({
            title: 'Vérification du statut FNE...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: this.baseURL + this.remoteAJAXFunctions.checkFNEStatus + '/' + invoiceId,
            type: 'GET',
            dataType: 'json',
            success: (response) => {
                Swal.close();
                if(response.status === "success") {
                    this.showInvoiceFNEStatus(response);
                } else {
                    this.showError(response.message);
                }
            },
            error: (xhr, status, error) => {
                Swal.close();
                this.showError('Erreur lors de la vérification du statut FNE');
            }
        });
    }

    showInvoiceFNEStatus(data) {
        Swal.fire({
            title: 'Statut FNE - Facture',
            html: `
                <div class="text-start">
                    <p><strong>Référence FNE :</strong> ${data.fne_reference}</p>
                    <p><strong>Certifié le :</strong> ${data.certified_at}</p>
                    <p><strong>Token de vérification :</strong></p>
                    <div class="bg-light p-2 rounded">
                        <small>${data.fne_token}</small>
                    </div>
                    ${data.fne_balance_sticker ?
                `<p><strong>Solde stickers :</strong> ${data.fne_balance_sticker}</p>` : ''
            }
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Fermer',
            width: '600px'
        });
    }

    showSuccess(message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        Toast.fire({ icon: 'success', title: message });
    }

    showError(message) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000
        });
        Toast.fire({ icon: 'error', title: message });
    }

    refreshInvoiceTable() {
        if (typeof invoiceTable !== 'undefined') {
            invoiceTable.ajax.reload(null, false);
        }
    }
}

// Initialisation
$(document).ready(function() {
    new InvoiceFNEIntegration();
});