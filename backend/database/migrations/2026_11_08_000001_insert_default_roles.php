<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            1 => 'administrador',
            2 => 'gerente',
            3 => 'cajero',
        ] as $id => $nombre) {
            $existing = DB::table('rol')->where('id_rol', $id)->value('nombre');

            if ($existing === null) {
                DB::table('rol')->insert([
                    'id_rol' => $id,
                    'nombre' => $nombre,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('rol')->whereIn('id_rol', [2, 3])->delete();
    }
};