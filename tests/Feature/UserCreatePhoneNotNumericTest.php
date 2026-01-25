<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('Crear usuario falla si phone no es numérico', function () {

    //1) Crear un usuario autenticado
    $authUser = User::factory()->create();

    //2) Crear rol válido
    $role = Role::firstOrCreate(['name' => 'Admin']);

    //3) Simulamos que ya inició sesión
    $this->actingAs($authUser, 'web');

    //4) Petición POST con telefono inválido (tiene letras)
    $response = $this->post(route('admin.users.store'), [
        'name' => 'Usuario Prueba',
        'email' => 'phonealpha@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'id_number' => 'PH-12345',
        'phone' => '99912ABCD',
        'address' => 'Calle 12',
        'role_id' => $role->id,
    ]);

    //5) Esperamos error de validación (302)
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['phone']);

    //6) Verificar que no se creó el usuario en la bd
    $this->assertDatabaseMissing('users', [
        'email' => 'phonealpha@example.com',
    ]);
});
