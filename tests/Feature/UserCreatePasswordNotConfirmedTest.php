<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

//Usamos la cualidad para refrescar DB entre test
uses(RefreshDatabase::class);

test('Crear usuario falla si password no está confirmado', function () {

    //1) Crear un usuario autenticado
    $authUser = User::factory()->create();

    //2) Crear rol
    $role = Role::create(['name' => 'Admin']);

    //3) Inició sesión
    $this->actingAs($authUser, 'web');

    //4) Petición POST con password_confirmation distinta
    $response = $this->post(route('admin.users.store'), [
        'name' => 'Usuario Nuevo',
        'email' => 'passfail@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'OtraPassword123!',
        'id_number' => 'PWD-12345',
        'phone' => '9991234567',
        'address' => 'Calle 5',
        'role_id' => $role->id,
    ]);

    //5) Esperamos error de validación (302)
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['password']);

    //6) Verificar que NO se creó el usuario
    $this->assertDatabaseMissing('users', [
        'email' => 'passfail@example.com',
    ]);
});
