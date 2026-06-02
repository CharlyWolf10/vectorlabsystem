<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Producto;
use App\Models\Proveedor;

class PdfController extends Controller
{
    public function exportInventario()
    {
        $productos = Producto::all();
        
        $data = [
            'title' => 'Reporte de Inventario - Vector Lab',
            'date' => date('d/m/Y'),
            'productos' => $productos,
            'logo' => 'https://charlywolf10.github.io/VectorLab/assets/img/logo.png'
        ];
        
        $pdf = Pdf::loadView('pdf.inventario', $data);
        return $pdf->download('reporte_inventario_' . date('Y_m_d') . '.pdf');
    }

    public function exportCompras(Request $request)
    {
        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $proveedores = Proveedor::whereIn('id', $ids)->get();
        } else {
            $proveedores = Proveedor::all();
        }
        
        $data = [
            'title' => 'Directorio de Proveedores - Vector Lab',
            'date' => date('d/m/Y'),
            'proveedores' => $proveedores,
            'logo' => 'https://charlywolf10.github.io/VectorLab/assets/img/logo.png'
        ];
        
        $pdf = Pdf::loadView('pdf.compras', $data);
        return $pdf->download('directorio_proveedores_' . date('Y_m_d') . '.pdf');
    }

    public function exportClientes()
    {
        $clientes = \App\Models\Cliente::all();
        
        $data = [
            'title' => 'Directorio de Clientes - Vector Lab',
            'date' => date('d/m/Y'),
            'clientes' => $clientes,
            'logo' => 'https://charlywolf10.github.io/VectorLab/assets/img/logo.png'
        ];
        
        $pdf = Pdf::loadView('pdf.clientes', $data);
        return $pdf->download('directorio_clientes_' . date('Y_m_d') . '.pdf');
    }
}
