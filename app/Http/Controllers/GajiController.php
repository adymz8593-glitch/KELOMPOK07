<?php

namespace App\Http\Controllers;

use App\Models\Gaji;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Pastikan library dompdf sudah terinstall

class GajiController extends Controller
{
    public function index()
    {
        $gajis = Gaji::with('karyawan')->latest()->get();
        $karyawans = Karyawan::all();
        return view('admin.gaji', compact('gajis', 'karyawans'));
    }

    // Fungsi Cetak Slip PDF
    public function cetakPdf($id)
    {
        $gaji = Gaji::with('karyawan')->findOrFail($id);
        
        // Memanggil file view khusus untuk layout PDF
        $pdf = Pdf::loadView('admin.slip_gaji_pdf', compact('gaji'))->setPaper('a5', 'landscape');

        return $pdf->download('Slip_Gaji_'.$gaji->karyawan->nama_karyawan.'_'.$gaji->bulan.'.pdf');
    }
}