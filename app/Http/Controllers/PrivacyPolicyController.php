<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PrivacyPolicyController extends Controller
{
    /**
     * Tampilkan halaman publik Kebijakan Privasi (F-42).
     */
    public function show(): View
    {
        return view('privacy-policy');
    }
}
