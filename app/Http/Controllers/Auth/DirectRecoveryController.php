<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DirectRecoveryController extends Controller
{
    public function send(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $email = $request->email;
        $password = 'vector100698'; // Contraseña quemada a petición expresa del administrador

        try {
            Mail::raw("Hola, solicitaste recordar tus accesos.\n\nTu usuario es: admin@vectorlab.com\nTu contraseña es: {$password}\n\nSaludos,\nSistema Vector Lab", function ($message) use ($email) {
                $message->to($email)
                        ->subject('Tus credenciales de acceso - Vector Lab');
            });
            
            return back()->with('recoverSuccess', 'Contraseña enviada correctamente a ' . $email . '. Por favor revisa tu bandeja de entrada o la carpeta de SPAM.');
        } catch (\Exception $e) {
            Log::error('Error al enviar correo de recuperación directa: ' . $e->getMessage());
            return back()->with('recoverError', 'Se intentó enviar el correo, pero falta configurar la contraseña de Aplicación de Google (SMTP) en el servidor. Configúrala para que los correos salgan.');
        }
    }
}
