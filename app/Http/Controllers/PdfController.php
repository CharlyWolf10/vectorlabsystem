<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\BusinessProfile;

class PdfController extends Controller
{
    public function exportInventario(Request $request)
    {
        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $productos = Producto::whereIn('id', $ids)->get();
        } else {
            $productos = Producto::all();
        }
        
        $isFaltantes = $request->query('faltantes', 0) == 1;
        
        $perfil = BusinessProfile::first();
        
        $data = [
            'title' => $isFaltantes ? 'Reporte de Faltantes - Vector Lab' : 'Reporte de Inventario - Vector Lab',
            'date' => date('d/m/Y'),
            'productos' => $productos,
            'perfil' => $perfil,
            'logo' => $perfil && $perfil->logo_path ? asset($perfil->logo_path) : 'https://charlywolf10.github.io/VectorLab/assets/img/logo.png',
            'is_faltantes' => $isFaltantes
        ];
        
        $pdf = Pdf::loadView('pdf.inventario', $data);
        return $pdf->stream('reporte_inventario_' . date('Y_m_d') . '.pdf');
    }

    public function exportCompras(Request $request)
    {
        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $proveedores = Proveedor::whereIn('id', $ids)->get();
        } else {
            $proveedores = Proveedor::all();
        }
        
        $perfil = BusinessProfile::first();

        $data = [
            'title' => 'Directorio de Proveedores - Vector Lab',
            'date' => date('d/m/Y'),
            'proveedores' => $proveedores,
            'perfil' => $perfil,
            'logo' => $perfil && $perfil->logo_path ? asset($perfil->logo_path) : 'https://charlywolf10.github.io/VectorLab/assets/img/logo.png'
        ];
        
        $pdf = Pdf::loadView('pdf.compras', $data);
        return $pdf->stream('directorio_proveedores_' . date('Y_m_d') . '.pdf');
    }

    public function exportClientes(Request $request)
    {
        if ($request->has('ids')) {
            $ids = explode(',', $request->ids);
            $clientes = \App\Models\Cliente::whereIn('id', $ids)->get();
        } else {
            $clientes = \App\Models\Cliente::all();
        }

        $perfil = BusinessProfile::first();

        $data = [
            'title' => 'Directorio de Clientes - Vector Lab',
            'date' => date('d/m/Y'),
            'clientes' => $clientes,
            'perfil' => $perfil,
            'logo' => $perfil && $perfil->logo_path ? asset($perfil->logo_path) : 'https://charlywolf10.github.io/VectorLab/assets/img/logo.png'
        ];
        
        $pdf = Pdf::loadView('pdf.clientes', $data);
        return $pdf->stream('directorio_clientes_' . date('Y_m_d') . '.pdf');
    }

    public function exportHistorial(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);
        $filtro = $request->get('filtro');
        
        $query = \App\Models\HistorialInventario::where('producto_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc');
            
        if (!empty($filtro)) {
            $query->where('accion', $filtro);
        }
        
        $historial = $query->get();
        
        $perfil = BusinessProfile::first();
            
        $data = [
            'title' => 'Historial de Inventario: ' . $producto->nombre,
            'date' => date('d/m/Y'),
            'producto' => $producto,
            'historial' => $historial,
            'perfil' => $perfil,
            'logo' => $perfil && $perfil->logo_path ? asset($perfil->logo_path) : 'https://charlywolf10.github.io/VectorLab/assets/img/logo.png'
        ];
        
        $pdf = Pdf::loadView('pdf.historial', $data);
        return $pdf->stream('historial_producto_' . $producto->codigo . '_' . date('Y_m_d') . '.pdf');
    }
}
