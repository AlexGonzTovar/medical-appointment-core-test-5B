<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Un usuario no puede editarse a sí mismo', function () {
    // Crear un usuario de prueba
    $user = User::factory()->create();

    // Simular que el usuario está logueado
    $this->actingAs($user);

    // Intentar acceder al formulario de edición de sí mismo
    $response = $this->get(route('admin.users.edit', $user));

    // Esperamos que el servidor responda con un código 403 (Prohibido)
    $response->assertStatus(403);
});
