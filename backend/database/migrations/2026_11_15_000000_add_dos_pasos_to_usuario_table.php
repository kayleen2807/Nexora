<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Verificación en dos pasos por correo: cada usuario la activa o desactiva en "Configuración".
 * Con ella activa, después de la contraseña (o de Google) se pide un código de 6 dígitos que llega al correo.
 *
 * Columnas separadas de email_verification_* a propósito: esas son para confirmar el correo al
 * registrarse; si se compartieran, pedir un código de inicio de sesión borraría uno de registro.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            if (! Schema::hasColumn('usuario', 'dos_pasos_activo')) {
                $table->boolean('dos_pasos_activo')->default(false);
            }
            if (! Schema::hasColumn('usuario', 'dos_pasos_codigo')) {
                $table->string('dos_pasos_codigo')->nullable();     // el código se guarda encriptado (Hash), nunca en texto
            }
            if (! Schema::hasColumn('usuario', 'dos_pasos_expira')) {
                $table->timestamp('dos_pasos_expira')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            foreach (['dos_pasos_activo', 'dos_pasos_codigo', 'dos_pasos_expira'] as $columna) {
                if (Schema::hasColumn('usuario', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }
};
