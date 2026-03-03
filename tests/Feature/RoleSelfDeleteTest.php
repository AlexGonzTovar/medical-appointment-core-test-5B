<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Los primeros 4 roles no se pueden eliminar', function () {
    // Crear un usuario admin para hacer la petición
    $admin = User::factory()->create();

    // Crear los 4 roles protegidos
    $rol1 = Role::create(['name' => 'admin']);
    $rol2 = Role::create(['name' => 'doctor']);
    $rol3 = Role::create(['name' => 'paciente']);
    $rol4 = Role::create(['name' => 'recepcionista']);

    // Intentar eliminar cada rol protegido
    foreach ([$rol1, $rol2, $rol3, $rol4] as $rol) {
        $response = $this->actingAs($admin)->delete(route('admin.roles.destroy', $rol));

        // Debe redirigir al index (no permitió eliminar)
        $response->assertRedirect(route('admin.roles.index'));

        // El rol debe seguir existiendo en la base de datos
        $this->assertDatabaseHas('roles', ['id' => $rol->id]);
    }
});
