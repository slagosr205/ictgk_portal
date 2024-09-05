<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
class PermissionsDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        // create permissions
        Permission::create(['name' => 'see graph']);
        $role1 = Role::create(['name' => 'admin']);
        $user = User::where('email', 'admin@admin.com')->first();
        // Verificar si el usuario existe
        if ($user) {
            // Modificar los campos necesarios
            $user->assignRole($role1);

            // Guardar los cambios en la base de datos
            $user->save();

            echo "Usuario modificado exitosamente.";
        } else {
            echo "El usuario no existe.";
        }
    }
}
