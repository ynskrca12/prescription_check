<?php

namespace App\Http\Controllers;

use App\Models\Physician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;


class PhysicianAuthController extends Controller
{
    /**
     * Login sayfasını göster
     */
    public function showLogin()
    {
        if (auth('physician')->check()) {
            return redirect()->route('prescription.index');
        }

        return view('physician.login');
    }

    /**
     * QR Kod sayfasını göster
     */
public function showQrCode()
{
    $physicians = \App\Models\Physician::where('is_active', true)
        ->orderBy('physician_code')
        ->get();

    $writer = new PngWriter();

    foreach ($physicians as $physician) {
        $url = route('physician.password', $physician->physician_code);

        // QrCode nesnesi oluştur
        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            // errorCorrectionLevel: new ErrorCorrectionLevelHigh(),
            size: 150,
            margin: 5
        );

        // Görsel oluştur
        $result = $writer->write($qrCode);

        // Base64 olarak Blade'e gönder
        $physician->qr = 'data:image/png;base64,' . base64_encode($result->getString());
    }

    return view('physician.qr-codes', compact('physicians'));
}

    /**
     * QR kod okutulduktan sonra şifre ekranı
     */
    public function showPasswordForm($physicianCode)
    {
        $physician = Physician::where('physician_code', $physicianCode)
            ->where('is_active', true)
            ->first();

        if (!$physician) {
            return redirect()->route('physician.login')
                ->with('error', 'Geçersiz hekim kodu!');
        }

        return view('physician.password', compact('physician'));
    }

    /**
     * Login işlemi
     */
    public function login(Request $request)
    {
        $request->validate([
            'physician_code' => 'required|string',
            'password' => 'required|string',
        ]);

        $physician = Physician::where('physician_code', $request->physician_code)
            ->where('is_active', true)
            ->first();

        if (!$physician || !Hash::check($request->password, $physician->password)) {
            return back()
                ->withInput($request->only('physician_code'))
                ->with('error', 'Hekim kodu veya şifre hatalı!');
        }

        // Login
        Auth::guard('physician')->login($physician, $request->filled('remember'));

        // Son giriş tarihini güncelle
        $physician->updateLastLogin();

        return redirect()->route('prescription.index')
            ->with('success', 'Hoş geldiniz, Dr. ' . $physician->full_name);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('physician')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('physician.login')
            ->with('success', 'Başarıyla çıkış yaptınız.');
    }
}
