<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Services\NamecardService;
use App\Models\Employee;

class NamecardController extends Controller
{
    protected $namecardService;

    public function __construct(NamecardService $namecardService)
    {
        $this->namecardService = $namecardService;
    }

    public function showNamecard()
    
    {
        $employee = Auth::user();

        if (empty($employee->name) || empty($employee->phone)) {
            return redirect()->route('profile.view')->with('error', 'Please complete your profile details to generate a QR code.');
        }

        $pageTitle = 'Namecard';
        $vCard = $this->namecardService->generateVCard($employee->name, $employee->phone);
        $qrCode = $this->namecardService->generateQrCode($vCard);

        return view('namecard', compact('employee', 'pageTitle', 'qrCode'));
    }

    public function downloadVCard($name, $phone)
    {
        $vCardContent = $this->namecardService->generateVCard($name, $phone);
        $filename = $name . '.vcf';

        return response($vCardContent, 200)
            ->header('Content-Type', 'text/x-vcard')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function showVCardDownloadPage(Employee $employee)
    {
        if ($employee->is_active != 1) {
            throw new \Exception('Your account is inactive.');
        }
        return view('vcard_download_page', compact('employee'));
    }
}
