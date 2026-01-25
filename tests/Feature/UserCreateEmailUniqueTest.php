<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Usamos la cualidad para refrescar DB entre test
uses(RefreshDatabase::class);

test('Crear usuario falla si el email ya existe', function () {

    //1) Crear un usuario autenticado (quien intenta crear)
    $authUser = User::factory()->create();

    //2) Crear un usuario existente con un email específico
    User::factory()->create([
        'email' => 'duplicado@example.com',
    ]);

    //3) Iniciar sesión
    $this->actingAs($authUser, 'web');

    //4) Petición HTTP POST para crear un usuario con email duplicado
    $response = $this->post(route('admin.users.store'), [
        'name' => 'Nuevo Usuario',
        'email' => 'duplicado@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    //5) El servidor rechaza por validación (normalmente 302 en web)
    $response->assertStatus(302);
    $response->assertSessionHasErrors(['email']);

    //6) Verificar que no se creó otro usuario con el mismo email (sigue existiendo el original)
    $this->assertDatabaseHas('users', [
        'email' => 'duplicado@example.com',
    ]);
});
