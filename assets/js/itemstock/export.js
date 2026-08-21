// Export functions for Stock State
class StockExporter {
    constructor(dataTable) {
        this.dataTable = dataTable;
        this.initializeEvents();
        console.log('StockExporter initialized');
    }

    initializeEvents() {
        // Excel export
        $('#export-excel').on('click', (e) => {
            e.preventDefault();
            this.exportToExcel();
        });

        // PDF export with options
        $('#export-pdf').on('click', (e) => {
            e.preventDefault();
            $('#pdfOptionsModal').modal('show');
        });

        // Print
        $('#export-print').on('click', (e) => {
            e.preventDefault();
            this.printTable();
        });

        // Generate PDF with options
        $('#generatePdf').on('click', () => {
            const formData = {};
            $('#pdfOptionsForm').serializeArray().forEach(function(item) {
                if (item.name.includes('include_')) {
                    formData[item.name] = item.value === 'on';
                } else {
                    formData[item.name] = item.value;
                }
            });

            $('#pdfOptionsModal').modal('hide');
            this.exportToPDF(formData);
        });

        // Reset form when modal is hidden
        $('#pdfOptionsModal').on('hidden.bs.modal', function() {
            $('#pdfOptionsForm')[0].reset();
        });
    }

    showLoading(message = 'Génération du rapport...') {
        $('#export-status').html('<i class="fa fa-spinner fa-spin"></i> ' + message).fadeIn();
    }

    hideLoading() {
        $('#export-status').fadeOut();
    }

    getFormattedData() {
        const table = $('#stockTable');
        const headers = [];
        const data = [];
        const rawData = [];

        // Get headers
        table.find('thead th').each(function() {
            if ($(this).attr('colspan') !== '4') {
                headers.push($(this).text().trim());
            }
        });

        // Get data from DataTable or HTML table
        if (this.dataTable && this.dataTable.rows) {
            // Utiliser DataTable API
            const dtData = this.dataTable.rows({ filter: 'applied' }).data();

            dtData.each((row, index) => {
                const rowData = [];
                if (row.article) {
                    // Format DataTable row
                    rowData.push(row.article || '');
                    rowData.push(row.categorie || '');
                    rowData.push(row.unite || '');

                    // Format cost
                    const cost = parseFloat(row.cout_moyen || 0);
                    rowData.push(cost.toLocaleString('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' FCFA');

                    // Format quantity
                    const qty = parseFloat(row.quantite || 0);
                    rowData.push(qty.toLocaleString('fr-FR'));

                    rawData.push({
                        article: row.article,
                        categorie: row.categorie,
                        unite: row.unite,
                        cout_moyen: cost,
                        quantite: qty
                    });
                } else {
                    // Format array row
                    for (let i = 0; i < 5; i++) {
                        rowData.push(row[i] || '');
                    }

                    rawData.push({
                        article: row[0] || '',
                        categorie: row[1] || '',
                        unite: row[2] || '',
                        cout_moyen: parseFloat((row[3] || '').replace(/[^\d.,]/g, '').replace(',', '.')) || 0,
                        quantite: parseFloat((row[4] || '').replace(/[^\d.,]/g, '').replace(',', '.')) || 0
                    });
                }
                data.push(rowData);
            });
        } else {
            // Fallback: get data from HTML table
            table.find('tbody tr').each(function() {
                const row = [];
                const rawRow = {};
                $(this).find('td').each(function(index) {
                    const cellText = $(this).text().trim();
                    row.push(cellText);

                    // Store raw values for calculations
                    switch(index) {
                        case 0: rawRow.article = cellText; break;
                        case 1: rawRow.categorie = cellText; break;
                        case 2: rawRow.unite = cellText; break;
                        case 3:
                            rawRow.cout_moyen = parseFloat(cellText.replace(/[^\d.,]/g, '').replace(',', '.')) || 0;
                            break;
                        case 4:
                            rawRow.quantite = parseFloat(cellText.replace(/[^\d.,]/g, '').replace(',', '.')) || 0;
                            break;
                    }
                });
                if (row.length > 0) {
                    data.push(row);
                    rawData.push(rawRow);
                }
            });
        }

        return {
            headers: headers.filter(h => h !== ''),
            data: data,
            rawData: rawData
        };
    }

    exportToExcel() {
        this.showLoading('Préparation du fichier Excel...');

        const { headers, data } = this.getFormattedData();

        // Create workbook
        const wb = XLSX.utils.book_new();

        // Create worksheet with data
        const wsData = [headers, ...data];
        const ws = XLSX.utils.aoa_to_sheet(wsData);

        // Set column widths
        const colWidths = [
            { wch: 25 }, // Article
            { wch: 20 }, // Catégorie
            { wch: 10 }, // Unité
            { wch: 20 }, // Coût moyen
            { wch: 20 }  // Quantité
        ];
        ws['!cols'] = colWidths;

        // Style the header row
        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let C = range.s.c; C <= range.e.c; ++C) {
            const address = XLSX.utils.encode_cell({ r: 0, c: C });
            if (!ws[address]) continue;
            ws[address].s = {
                fill: {
                    patternType: "solid",
                    fgColor: { rgb: "4472C4" }
                },
                font: {
                    bold: true,
                    color: { rgb: "FFFFFF" },
                    sz: 11
                },
                alignment: {
                    horizontal: "center",
                    vertical: "center"
                },
                border: {
                    top: { style: "thin", color: { rgb: "000000" } },
                    bottom: { style: "thin", color: { rgb: "000000" } },
                    left: { style: "thin", color: { rgb: "000000" } },
                    right: { style: "thin", color: { rgb: "000000" } }
                }
            };
        }

        // Style data rows
        for (let R = 1; R <= range.e.r; ++R) {
            for (let C = range.s.c; C <= range.e.c; ++C) {
                const address = XLSX.utils.encode_cell({ r: R, c: C });
                if (!ws[address]) continue;

                ws[address].s = {
                    border: {
                        left: { style: "thin", color: { rgb: "D9D9D9" } },
                        right: { style: "thin", color: { rgb: "D9D9D9" } },
                        top: { style: "thin", color: { rgb: "D9D9D9" } },
                        bottom: { style: "thin", color: { rgb: "D9D9D9" } }
                    }
                };

                // Right align for numeric columns
                if (C === 3 || C === 4) {
                    ws[address].s.alignment = { horizontal: "right" };
                }

                // Alternate row coloring
                if (R % 2 === 0) {
                    ws[address].s.fill = {
                        patternType: "solid",
                        fgColor: { rgb: "F2F2F2" }
                    };
                }
            }
        }

        // Add totals row
        const totalRow = range.e.r + 1;
        ws['!ref'] = XLSX.utils.encode_range({
            s: range.s,
            e: { r: totalRow, c: range.e.c }
        });

        // Add total label
        const totalLabelCell = XLSX.utils.encode_cell({ r: totalRow, c: 3 });
        ws[totalLabelCell] = { v: "TOTAL", t: "s" };
        ws[totalLabelCell].s = {
            font: { bold: true },
            alignment: { horizontal: "right" },
            fill: {
                patternType: "solid",
                fgColor: { rgb: "F2F2F2" }
            }
        };

        // Calculate and add total quantity
        const totalQty = data.reduce((sum, row) => {
            const qtyStr = row[4] || '0';
            const qty = parseFloat(qtyStr.replace(/[^\d.,]/g, '').replace(',', '.')) || 0;
            return sum + qty;
        }, 0);

        const totalCell = XLSX.utils.encode_cell({ r: totalRow, c: 4 });
        ws[totalCell] = { v: totalQty, t: "n" };
        ws[totalCell].s = {
            font: { bold: true },
            alignment: { horizontal: "right" },
            numFmt: '#,##0',
            fill: {
                patternType: "solid",
                fgColor: { rgb: "F2F2F2" }
            }
        };

        // Add to workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Etat_de_Stock');

        // Add a summary sheet
        const summaryData = [
            ["RÉSUMÉ DU STOCK", ""],
            ["Date de génération", new Date().toLocaleDateString('fr-FR')],
            ["Heure de génération", new Date().toLocaleTimeString('fr-FR')],
            ["Nombre d'articles", data.length],
            ["Quantité totale", totalQty],
            ["", ""],
            ["Généré par", "Système de Gestion de Stock"]
        ];

        const summaryWs = XLSX.utils.aoa_to_sheet(summaryData);
        summaryWs['!cols'] = [{ wch: 25 }, { wch: 25 }];
        XLSX.utils.book_append_sheet(wb, summaryWs, 'Résumé');

        // Generate and download
        const wbout = XLSX.write(wb, {
            bookType: 'xlsx',
            type: 'binary',
            bookSST: false
        });

        function s2ab(s) {
            const buf = new ArrayBuffer(s.length);
            const view = new Uint8Array(buf);
            for (let i = 0; i < s.length; i++) {
                view[i] = s.charCodeAt(i) & 0xFF;
            }
            return buf;
        }

        const filename = `Etat_Stock_${new Date().toISOString().split('T')[0]}.xlsx`;
        saveAs(new Blob([s2ab(wbout)], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        }), filename);

        setTimeout(() => {
            this.hideLoading();
            this.showSuccess('Fichier Excel généré avec succès !');
        }, 500);
    }

    exportToPDF(options) {
        this.showLoading('Création du PDF...');

        // Use setTimeout to allow UI to update
        setTimeout(() => {
            try {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({
                    orientation: options.orientation || 'portrait',
                    unit: 'mm',
                    format: options.format || 'A4',
                    compress: true
                });

                const { headers, data, rawData } = this.getFormattedData();
                const title = options.report_title || 'État de Stock';
                const includeDate = options.include_date !== false;
                const includeSummary = options.include_summary !== false;
                const includeFooter = options.include_footer !== false;

                // Set default font
                doc.setFont("helvetica");

                // Calculate page dimensions
                const pageWidth = doc.internal.pageSize.width;
                const pageHeight = doc.internal.pageSize.height;
                let yPos = 20;

                // Add decorative header
                doc.setFillColor(41, 128, 185);
                doc.rect(0, 0, pageWidth, 15, 'F');

                // Title in header
                doc.setFontSize(18);
                doc.setTextColor(255, 255, 255);
                doc.setFont("helvetica", "bold");
                doc.text(title, pageWidth / 2, 10, { align: 'center' });

                yPos = 25;

                // Date if included
                if (includeDate) {
                    doc.setFontSize(10);
                    doc.setTextColor(100, 100, 100);
                    doc.setFont("helvetica", "normal");
                    const dateStr = `Généré le : ${new Date().toLocaleDateString('fr-FR', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })} à ${new Date().toLocaleTimeString('fr-FR', {
                        hour: '2-digit',
                        minute: '2-digit'
                    })}`;
                    doc.text(dateStr, pageWidth / 2, yPos, { align: 'center' });
                    yPos += 8;
                }

                // Calculate totals for summary
                let totalItems = rawData.length;
                let totalQuantity = 0;
                let totalValue = 0;
                let maxCost = 0;
                let minCost = Infinity;

                rawData.forEach(row => {
                    const quantity = row.quantite || 0;
                    const cost = row.cout_moyen || 0;
                    totalQuantity += quantity;
                    totalValue += quantity * cost;

                    if (cost > maxCost) maxCost = cost;
                    if (cost < minCost && cost > 0) minCost = cost;
                });

                if (minCost === Infinity) minCost = 0;

                // Add summary if included
                if (includeSummary) {
                    // Summary box
                    doc.setFillColor(245, 245, 245);
                    doc.roundedRect(10, yPos, pageWidth - 20, 30, 3, 3, 'F');

                    doc.setFontSize(12);
                    doc.setTextColor(41, 128, 185);
                    doc.setFont("helvetica", "bold");
                    doc.text("RÉSUMÉ", 15, yPos + 8);

                    doc.setFontSize(9);
                    doc.setTextColor(60, 60, 60);
                    doc.setFont("helvetica", "normal");

                    // Column 1
                    doc.text(`Articles différents: ${totalItems}`, 15, yPos + 16);
                    doc.text(`Quantité totale: ${totalQuantity.toLocaleString('fr-FR')}`, 15, yPos + 22);

                    // Column 2
                    const col2X = pageWidth / 2;
                    doc.text(`Valeur totale: ${totalValue.toLocaleString('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })} FCFA`, col2X, yPos + 16);

                    doc.text(`Coût moyen: ${(totalValue / totalQuantity || 0).toLocaleString('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })} FCFA`, col2X, yPos + 22);

                    yPos += 35;
                }

                // Add table with improved styling
                const tableOptions = {
                    head: [headers],
                    body: data,
                    startY: yPos,
                    theme: 'grid',
                    headStyles: {
                        fillColor: [52, 152, 219],
                        textColor: 255,
                        fontStyle: 'bold',
                        fontSize: 10,
                        cellPadding: 3,
                        lineWidth: 0.1
                    },
                    bodyStyles: {
                        fontSize: 9,
                        cellPadding: 3,
                        lineWidth: 0.1,
                        lineColor: [220, 220, 220]
                    },
                    alternateRowStyles: {
                        fillColor: [248, 248, 248]
                    },
                    columnStyles: {
                        0: { cellWidth: 40, fontStyle: 'bold' },
                        3: { cellWidth: 25, halign: 'right' },
                        4: {
                            cellWidth: 25,
                            halign: 'right',
                            fontStyle: 'bold'
                        }
                    },
                    margin: { top: 5 },
                    styles: {
                        overflow: 'linebreak',
                        cellWidth: 'wrap'
                    },
                    didDrawPage: function(pageData) {
                        // Footer on each page
                        if (includeFooter) {
                            doc.setFontSize(8);
                            doc.setTextColor(150, 150, 150);
                            doc.setFont("helvetica", "normal");

                            // Left footer
                            doc.text("Système de Gestion de Stock", 10, pageHeight - 10);

                            // Center footer - page number
                            doc.text(
                                `Page ${doc.internal.getNumberOfPages()}`,
                                pageWidth / 2,
                                pageHeight - 10,
                                { align: 'center' }
                            );

                            // Right footer
                            doc.text(
                                `Document confidentiel`,
                                pageWidth - 10,
                                pageHeight - 10,
                                { align: 'right' }
                            );

                            // Footer line
                            doc.setDrawColor(200, 200, 200);
                            doc.line(10, pageHeight - 15, pageWidth - 10, pageHeight - 15);
                        }
                    },
                    willDrawCell: function(data) {
                        // Highlight low stock items (less than 10)
                        if (data.column.index === 4 && data.row.index > 0) {
                            const qty = parseInt(data.cell.raw.toString().replace(/[^\d]/g, '')) || 0;
                            if (qty < 10) {
                                doc.setTextColor(231, 76, 60); // Red color for low stock
                            } else if (qty > 100) {
                                doc.setTextColor(39, 174, 96); // Green color for high stock
                            }
                        }
                    },
                    didParseCell: function(data) {
                        // Reset text color after each cell
                        if (data.row.index > 0) {
                            data.cell.styles.textColor = [0, 0, 0];
                        }
                    }
                };

                doc.autoTable(tableOptions);

                // Add final summary after table
                const finalY = doc.lastAutoTable.finalY || yPos;
                if (finalY < pageHeight - 30) {
                    doc.setFontSize(10);
                    doc.setTextColor(41, 128, 185);
                    doc.setFont("helvetica", "bold");
                    doc.text("STATISTIQUES", pageWidth / 2, finalY + 15, { align: 'center' });

                    doc.setFontSize(9);
                    doc.setTextColor(60, 60, 60);
                    doc.setFont("helvetica", "normal");

                    const stats = [
                        `Article avec stock le plus élevé: ${Math.max(...rawData.map(r => r.quantite))}`,
                        `Article avec le coût le plus élevé: ${maxCost.toLocaleString('fr-FR', {minimumFractionDigits: 2})} FCFA`,
                        `Stock moyen par article: ${(totalQuantity / totalItems).toLocaleString('fr-FR', {minimumFractionDigits: 1})}`
                    ];

                    stats.forEach((stat, index) => {
                        doc.text(stat, pageWidth / 2, finalY + 22 + (index * 5), { align: 'center' });
                    });
                }

                // Save PDF
                const filename = `Etat_Stock_${new Date().toISOString().split('T')[0]}.pdf`;
                doc.save(filename);

                this.hideLoading();
                this.showSuccess('PDF généré avec succès !');

            } catch (error) {
                console.error('Erreur lors de la génération du PDF:', error);
                this.hideLoading();
                this.showError('Erreur lors de la génération du PDF: ' + error.message);
            }
        }, 100);
    }

    printTable() {
        this.showLoading('Préparation de l\'impression...');

        setTimeout(() => {
            const originalTitle = document.title;
            document.title = 'État de Stock - ' + new Date().toLocaleDateString('fr-FR');

            const { headers, data, rawData } = this.getFormattedData();

            // Calculate totals
            let totalItems = rawData.length;
            let totalQuantity = 0;
            let totalValue = 0;

            rawData.forEach(row => {
                const quantity = row.quantite || 0;
                const cost = row.cout_moyen || 0;
                totalQuantity += quantity;
                totalValue += quantity * cost;
            });

            // Create print window
            const printWindow = window.open('', '_blank', 'width=800,height=600');

            printWindow.document.write(`
                <!DOCTYPE html>
                <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <title>${document.title}</title>
                    <style>
                        @media print {
                            @page {
                                size: landscape;
                                margin: 15mm;
                            }
                            
                            body {
                                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                color: #333;
                                margin: 0;
                                padding: 0;
                            }
                            
                            .no-print {
                                display: none !important;
                            }
                        }
                        
                        body {
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                            margin: 20px;
                            color: #333;
                        }
                        
                        .print-header {
                            text-align: center;
                            margin-bottom: 30px;
                            padding-bottom: 20px;
                            border-bottom: 3px solid #3498db;
                        }
                        
                        .print-header h1 {
                            color: #2c3e50;
                            margin: 0 0 10px 0;
                            font-size: 28px;
                        }
                        
                        .print-header .date {
                            color: #7f8c8d;
                            font-size: 14px;
                            margin-bottom: 20px;
                        }
                        
                        .summary-box {
                            background: #f8f9fa;
                            border: 1px solid #dee2e6;
                            border-radius: 5px;
                            padding: 15px;
                            margin-bottom: 20px;
                            display: flex;
                            justify-content: space-around;
                            flex-wrap: wrap;
                        }
                        
                        .summary-item {
                            text-align: center;
                            padding: 10px;
                            min-width: 150px;
                        }
                        
                        .summary-value {
                            font-size: 24px;
                            font-weight: bold;
                            color: #3498db;
                            margin: 5px 0;
                        }
                        
                        .summary-label {
                            font-size: 12px;
                            color: #6c757d;
                            text-transform: uppercase;
                            letter-spacing: 1px;
                        }
                        
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin: 20px 0;
                            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                        }
                        
                        th {
                            background: linear-gradient(to bottom, #3498db, #2980b9);
                            color: white;
                            font-weight: bold;
                            padding: 12px 8px;
                            text-align: left;
                            border: 1px solid #2980b9;
                            font-size: 13px;
                        }
                        
                        td {
                            padding: 10px 8px;
                            border: 1px solid #dee2e6;
                            font-size: 12px;
                        }
                        
                        tr:nth-child(even) {
                            background-color: #f8f9fa;
                        }
                        
                        tr:hover {
                            background-color: #e3f2fd;
                        }
                        
                        .numeric {
                            text-align: right;
                            font-family: 'Courier New', monospace;
                        }
                        
                        .low-stock {
                            color: #e74c3c;
                            font-weight: bold;
                        }
                        
                        .high-stock {
                            color: #27ae60;
                        }
                        
                        .total-row {
                            background-color: #2c3e50 !important;
                            color: white;
                            font-weight: bold;
                        }
                        
                        .print-footer {
                            margin-top: 40px;
                            text-align: center;
                            font-size: 11px;
                            color: #95a5a6;
                            border-top: 1px solid #ecf0f1;
                            padding-top: 10px;
                        }
                        
                        .print-actions {
                            text-align: center;
                            margin: 20px 0;
                            padding: 20px;
                            background: #f8f9fa;
                            border-radius: 5px;
                        }
                        
                        .btn {
                            padding: 10px 20px;
                            margin: 0 10px;
                            border: none;
                            border-radius: 4px;
                            cursor: pointer;
                            font-weight: bold;
                        }
                        
                        .btn-print {
                            background: #3498db;
                            color: white;
                        }
                        
                        .btn-close {
                            background: #95a5a6;
                            color: white;
                        }
                    </style>
                </head>
                <body>
                    <div class="print-header">
                        <h1>${document.title.split(' - ')[0]}</h1>
                        <div class="date">Généré le ${new Date().toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            })} à ${new Date().toLocaleTimeString('fr-FR', {
                hour: '2-digit',
                minute: '2-digit'
            })}</div>
                    </div>
                    
                    <div class="summary-box">
                        <div class="summary-item">
                            <div class="summary-value">${totalItems}</div>
                            <div class="summary-label">Articles différents</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value">${totalQuantity.toLocaleString('fr-FR')}</div>
                            <div class="summary-label">Quantité totale</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value">${totalValue.toLocaleString('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })} FCFA</div>
                            <div class="summary-label">Valeur totale</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-value">${(totalValue / totalQuantity || 0).toLocaleString('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })} FCFA</div>
                            <div class="summary-label">Coût moyen</div>
                        </div>
                    </div>
                    
                    <table>
                        <thead>
                            <tr>
                                ${headers.map(header => `<th>${header}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody>
                            ${data.map((row, rowIndex) => `
                                <tr>
                                    ${row.map((cell, cellIndex) => {
                let cellClass = '';
                if (cellIndex === 3 || cellIndex === 4) {
                    cellClass = 'numeric';
                }
                if (cellIndex === 4) {
                    const qty = parseInt(cell.toString().replace(/[^\d]/g, '')) || 0;
                    if (qty < 10) cellClass += ' low-stock';
                    else if (qty > 100) cellClass += ' high-stock';
                }
                return `<td class="${cellClass}">${cell}</td>`;
            }).join('')}
                                </tr>
                            `).join('')}
                            <tr class="total-row">
                                <td colspan="4" style="text-align: right;">TOTAL QUANTITÉ :</td>
                                <td class="numeric">${totalQuantity.toLocaleString('fr-FR')}</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div class="print-footer">
                        Document généré automatiquement par le Système de Gestion de Stock<br>
                        Page 1/1 • ${new Date().toLocaleDateString('fr-FR')}
                    </div>
                    
                    <div class="print-actions no-print">
                        <button class="btn btn-print" onclick="window.print()">
                            <i class="fa fa-print"></i> Imprimer
                        </button>
                        <button class="btn btn-close" onclick="window.close()">
                            <i class="fa fa-times"></i> Fermer
                        </button>
                    </div>
                    
                    <script>
                        // Auto-print after loading
                        window.onload = function() {
                            setTimeout(() => {
                                window.print();
                            }, 500);
                        };
                    </script>
                </body>
                </html>
            `);

            printWindow.document.close();
            document.title = originalTitle;
            this.hideLoading();
        }, 500);
    }

    showSuccess(message) {
        const alert = $(`
            <div class="alert alert-success alert-dismissible" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-check-circle"></i> ${message}
            </div>
        `);
        $('body').append(alert);
        setTimeout(() => alert.remove(), 3000);
    }

    showError(message) {
        const alert = $(`
            <div class="alert alert-danger alert-dismissible" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-exclamation-circle"></i> ${message}
            </div>
        `);
        $('body').append(alert);
        setTimeout(() => alert.remove(), 5000);
    }
}

// Initialize exporter when DataTable is ready
$(document).ready(function() {
    // Check if DataTable exists
    if ($.fn.dataTable.isDataTable('.itemStockDatatable')) {
        const dataTable = $('.itemStockDatatable').DataTable();
        window.stockExporter = new StockExporter(dataTable);
    } else {
        // Fallback for static tables
        window.stockExporter = new StockExporter(null);
    }
});