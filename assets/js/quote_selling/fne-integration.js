"use strict";

class FNEIntegration {
    constructor() {
        this.baseURL = base_url; // Assurez-vous que base_url est définie
        this.initEvents();
    }

    initEvents() {
        // Certifier un devis FNE
        $(document).on('click', '.certify-fne', (e) => this.certifyQuote(e));

        // Vérifier le statut FNE
        $(document).on('click', '.check-fne-status', (e) => this.checkStatus(e));
    }

    certifyQuote(e) {
        e.preventDefault();
        const quoteId = $(e.currentTarget).data('id');

        Swal.fire({
            title: 'Certification FNE',
            text: "Voulez-vous certifier ce devis auprès de la plateforme FNE ?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, certifier',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
            this.submitCertification(quoteId);
        }
    });
    }

    submitCertification(quoteId) {
        // Afficher l'indicateur de chargement
        Swal.fire({
            title: 'Certification en cours...',
            html: 'Connexion à la plateforme FNE en cours',
            allowOutsideClick: false,
            didOpen: () => {
            Swal.showLoading();
        }
    });

        $.ajax({
            url: this.baseURL + 'admin/FNE_controller/certifyQuote/' + quoteId,
            type: 'POST',
            dataType: 'json',
            success: (response) => {
            Swal.close();
            if(response.status === "success") {
            this.showSuccess(response.message);
            this.refreshTable();
        } else {
            this.showError(response.message);
        }
    },
        error: (xhr, status, error) => {
            Swal.close();
            this.showError('Erreur lors de la certification: ' + error);
        }
    });
    }

    checkStatus(e) {
        e.preventDefault();
        const quoteId = $(e.currentTarget).data('id');

        $.ajax({
            url: this.baseURL + 'admin/FNE_controller/checkCertificationStatus/' + quoteId,
            type: 'GET',
            dataType: 'json',
            success: (response) => {
            if(response.status === "success") {
            this.showFNEStatus(response);
        } else {
            this.showError(response.message);
        }
    }
    });
    }

    showFNEStatus(data) {
        Swal.fire({
            title: 'Statut FNE',
            html: `
                <div class="text-start">
                    <p><strong>Référence FNE:</strong> ${data.fne_reference}</p>
                    <p><strong>Certifié le:</strong> ${data.certified_at}</p>
                    <p><strong>Token:</strong> <small>${data.fne_token}</small></p>
                </div>
            `,
            icon: 'info'
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

    refreshTable() {
        if (typeof quoteTable !== 'undefined') {
            quoteTable.ajax.reload(null, false);
        }
    }
}

// Initialisation
$(document).ready(function() {
    new FNEIntegration();
});