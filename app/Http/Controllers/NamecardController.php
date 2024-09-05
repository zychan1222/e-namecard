<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Services\NamecardService;
use App\Models\Employee;
use App\Models\User;

class NamecardController extends Controller
{
    protected $namecardService;

    public function __construct(NamecardService $namecardService)
    {
        $this->namecardService = $namecardService;
    }

    public function showNamecard()
    {
        $employeeId = Session::get('employee_id');
        $employee = Employee::find($employeeId);

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        $user = User::find($employee->user_id);
        $email = $user ? $user->email : 'Email not available';

        $pageTitle = 'Namecard';
        $vCard = $this->namecardService->generateVCard($employee->name, $employee->phone);
        $qrCode = $this->namecardService->generateQrCode($vCard);

        return view('namecard', compact('employee', 'email', 'pageTitle', 'qrCode'));
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
            abort(403, 'The account is inactive.');
        }

        $employeeId = Session::get('employee_id');
        $employee = Employee::find($employeeId);
        $user = User::find($employee->user_id);
        $email = $user ? $user->email : 'Email not available';

        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }

        return view('vcard_download_page', compact('employee', 'email'));
    }
}
