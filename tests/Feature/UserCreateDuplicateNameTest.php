<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

//Usamos la cualidad para refrescar DB entre test
uses(RefreshDatabase::class);

test('Crear usuario falla si el name ya existe', function () {

    //1) Crear un usuario autenticado
    $authUser = User::factory()->create();

    //2) Crear rol y usuario existente con nombre repetido
    $role = Role::create(['name' => 'Admin']);
    User::factory()->create(['name' => 'Nombre Repetido']);

    //3) Inició sesión
    $this->actingAs($authUser, 'web');

    //4) Petición POST con nombre duplicado
    $response = $this->post(route('admin.users.store'), [
        'name' => 'Nombre Repetido',
        'email' => 'nuevo1@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'id_number' => 'ABC-12345',
        'phone' => '9991234567',
        'address' => 'Calle 1',
        'role_id' => $role->id,
    ]);

    //5) Esperamos error de validación (302)
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['name']);

    //6) Verificar que no se creó el usuario en la bd
    $this->assertDatabaseMissing('users', [
        'email' => 'nuevo1@example.com',
    ]);
});
