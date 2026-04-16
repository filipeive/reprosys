<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Adicionar o papel super_admin se não existir
        if (!DB::table('roles')->where('name', 'super_admin')->exists()) {
            DB::table('roles')->insert([
                'name' => 'super_admin',
                'description' => 'Acesso total ao sistema, gerencia administradores',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Atribuir o papel super_admin ao usuário ID 1 (assumindo que é o proprietário)
        $superAdminRole = DB::table('roles')->where('name', 'super_admin')->first();
        if ($superAdminRole) {
            DB::table('users')->where('id', 1)->update([
                'role_id' => $superAdminRole->id,
            ]);
        }
    }

    public function down()
    {
        // Reverter papel do usuário ID 1 para admin se necessário
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        if ($adminRole) {
            DB::table('users')->where('id', 1)->update([
                'role_id' => $adminRole->id,
            ]);
        }
        
        // Opcionalmente remover o papel super_admin
        // DB::table('roles')->where('name', 'super_admin')->delete();
    }
};
