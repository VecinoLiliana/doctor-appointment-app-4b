<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Usamos la cualidad para refrescar DB entre test
uses(RefreshDatabase::class);

test('Un usuario no puede eliminarse a sí mismo', function () {

    //1) Crear un usuario de prueba
    $user = User::factory()->create();

    //2) Simulamos que ya inició sesión
    $this->actingAs($user, 'web');

    //3) Simulamos una petición HTTP DELETE
    $response = $this->delete(route('admin.users.destroy', $user));

    //4) Esperamos que el servidor prohiba la acción (403 Forbidden)
    $response->assertStatus(403);

    //5) Verificar que el usuario siga existiedno en BD
    $this->assertDatabaseHas('users', [
        'id'=> $user->id
    ]);
});
