<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('load_phpspreadsheet')) {
    /**
     * Charge la bibliothèque PhpSpreadsheet
     */
    function load_phpspreadsheet() {
        // Chemin vers autoload Composer
        $composer_path = FCPATH . 'vendor/autoload.php';

        // Chemin alternatif si PhpSpreadsheet est installé manuellement
        $manual_path = APPPATH . 'third_party/PhpSpreadsheet/vendor/autoload.php';

        if (file_exists($composer_path)) {
            require_once $composer_path;
            return true;
        } elseif (file_exists($manual_path)) {
            require_once $manual_path;
            return true;
        } else {
            // Essayer de trouver autoload.php dans d'autres emplacements communs
            $paths = [
                APPPATH . '../vendor/autoload.php',
                dirname(APPPATH) . '/vendor/autoload.php',
                FCPATH . '../vendor/autoload.php'
            ];

            foreach ($paths as $path) {
                if (file_exists($path)) {
                    require_once $path;
                    return true;
                }
            }

            // Si PhpSpreadsheet n'est pas trouvé, on essaie une alternative simple
            log_message('error', 'PhpSpreadsheet non trouvé. Utilisation du mode simple.');
            return false;
        }
    }
}

if (!function_exists('read_excel_file_simple')) {
    /**
     * Fonction simple pour lire les fichiers Excel (alternative si PhpSpreadsheet n'est pas installé)
     *
     * @param string $file_path Chemin du fichier
     * @return array|false
     */
    function read_excel_file_simple($file_path) {
        // Si le fichier est un CSV, on peut le lire directement
        if (pathinfo($file_path, PATHINFO_EXTENSION) === 'csv') {
            return read_csv_file($file_path);
        }

        // Pour les fichiers Excel, on retourne false si PhpSpreadsheet n'est pas installé
        return false;
    }
}

if (!function_exists('read_csv_file')) {
    /**
     * Lit un fichier CSV
     *
     * @param string $file_path Chemin du fichier CSV
     * @return array
     */
    function read_csv_file($file_path) {
        $data = [];

        if (($handle = fopen($file_path, "r")) !== FALSE) {
            $row_num = 0;
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $data[] = $row;
                $row_num++;
            }
            fclose($handle);
        }

        return $data;
    }
}

if (!function_exists('create_excel_template')) {
    /**
     * Crée un modèle Excel simple (format CSV si PhpSpreadsheet n'est pas disponible)
     */
    function create_excel_template($file_path) {
        $headers = ['category', 'item', 'quantity', 'unit_price', 'notes'];
        $examples = [
            ['Fournitures de bureau', 'Stylo bleu', 100, 0.50, ''],
            ['Fournitures de bureau', 'Cahier A4', 50, 2.50, ''],
            ['Nettoyage', 'Désinfectant', 20, 15.00, ''],
            ['Informatique', 'Clé USB 16GB', 10, 8.00, '']
        ];

        // Si PhpSpreadsheet est disponible, on l'utilise
        if (load_phpspreadsheet()) {
            try {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();

                $sheet->fromArray($headers, NULL, 'A1');
                $sheet->fromArray($examples, NULL, 'A2');

                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
                $writer->save($file_path);

                return true;
            } catch (Exception $e) {
                log_message('error', 'Erreur création Excel: ' . $e->getMessage());
                return false;
            }
        } else {
            // Sinon, on crée un CSV
            $csv_path = str_replace('.xlsx', '.csv', $file_path);

            if (($handle = fopen($csv_path, 'w')) !== FALSE) {
                // Écrire les en-têtes
                fputcsv($handle, $headers);

                // Écrire les exemples
                foreach ($examples as $example) {
                    fputcsv($handle, $example);
                }

                fclose($handle);
                return true;
            }

            return false;
        }
    }
}