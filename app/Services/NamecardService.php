<?php
namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class NamecardService
{
    public function generateVCard($name, $phone)
    {
        $vCard = "BEGIN:VCARD\n";
        $vCard .= "VERSION:3.0\n";
        $vCard .= "FN:{$name}\n";
        $vCard .= "TEL:{$phone}\n";
        $vCard .= "END:VCARD";

        return $vCard;
    }

    public function generateQrCode($vCard)
    {
        return QrCode::size(100)->generate($vCard);
    }
}