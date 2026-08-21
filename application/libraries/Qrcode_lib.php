<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Charger Composer autoload (QR Code library)
if (file_exists(FCPATH . 'vendor/autoload.php')) {
    require_once FCPATH . 'vendor/autoload.php';
}

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class Qrcode_lib
{
    protected $qrDir;

    public function __construct()
    {
        // Répertoire où stocker les QR Codes
        $this->qrDir = FCPATH . "uploads/qrcodes/";

        if (!file_exists($this->qrDir)) {
            mkdir($this->qrDir, 0777, true);
        }
    }

    /**
     * Génère un QR code et retourne son URL
     * @param string $deliveryNumber
     * @param string $customerFullname
     * @param string $quoteNumber
     * @param string $customerFullname
     * @return string URL du QR code
     */
    public function generate($deliveryNumber, $customerFullname = "")
    {
        $qrFile = $this->qrDir . "qr_" . $deliveryNumber . ".png";

        // Créer le contenu du QR
        //$qrContent = "Bon de Livraison N° " . $deliveryNumber;
        $qrContent = $deliveryNumber;
        if (!empty($customerFullname)) {
            $qrContent .= "\nClient : " . $customerFullname;
        }

        // Créer le QR code
        $qrCode = new QrCode($qrContent);
        $qrCode->setSize(150);
        $qrCode->setMargin(5);
        $qrCode->setWriterByName('png');

        // Sauvegarde du fichier
        $qrCode->writeFile($qrFile);

        return base_url("uploads/qrcodes/qr_" . $deliveryNumber . ".png");
    }

    public function generate_quote($quoteNumber, $customerFullname = "")
    {
        $qrFile = $this->qrDir . "qr_" . $quoteNumber . ".png";

        // Créer le contenu du QR
        $qrContent = $quoteNumber;
        if (!empty($customerFullname)) {
            $qrContent .= "\nClient : " . $customerFullname;
        }

        // Créer le QR code
        $qrCode = new QrCode($qrContent);
        $qrCode->setSize(150);
        $qrCode->setMargin(5);
        $qrCode->setWriterByName('png');

        // Sauvegarde du fichier
        $qrCode->writeFile($qrFile);

        return base_url("uploads/qrcodes/qr_" . $quoteNumber . ".png");
    }

}
