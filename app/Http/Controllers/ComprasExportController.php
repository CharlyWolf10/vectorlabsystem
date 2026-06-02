<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CuentaPorPagar;
use Barryvdh\DomPDF\Facade\Pdf;

class ComprasExportController extends Controller
{
    public function exportPdf()
    {
        $cuentas = CuentaPorPagar::with('proveedor', 'compra')->get();

        $pdf = Pdf::loadView('pdf.compras', compact('cuentas'));
        
        return $pdf->download('reporte_cuentas_por_pagar.pdf');
    }
}
