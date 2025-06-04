<?php

namespace App\Controllers;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;

use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeController extends Admin_Controller
{
    public function index()
    {
        return view('qr_form');
    }

    public function generateQr()
    {
        // Retrieve form data
        $data = $this->request->getPost('data');

        // Create QR code
        $qrCode = new QrCode($data);
        $qrCode->setSize(300); // Set QR code size
        $qrCode->setErrorCorrectionLevel(new ErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)); // Set error correction level

        // Save QR code to a file
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Save QR code to writable directory
        $qrPath = WRITEPATH . 'uploads/qr_codes/' . uniqid('qr_', true) . '.png';
        $result->saveToFile($qrPath);

        // Pass QR code path to the view
        return redirect()->to('/qr-form')->with('qrPath', $qrPath);
    }
}