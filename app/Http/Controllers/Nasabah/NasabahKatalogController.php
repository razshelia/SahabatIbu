<?php

namespace App\Http\Controllers\Nasabah;

use App\Http\Controllers\Controller;
use App\Services\KatalogHargaService;
use Illuminate\View\View;

class NasabahKatalogController extends Controller
{
    public function __construct(
        protected KatalogHargaService $katalogService
    ) {}

    /**
     * Tampilkan katalog harga sampah berlaku bagi nasabah (F-15).
     */
    public function index(): View
    {
        $katalog = $this->katalogService->getKatalogBerlaku();

        return view('nasabah.katalog.index', compact('katalog'));
    }
}
