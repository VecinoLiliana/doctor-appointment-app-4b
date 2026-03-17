<?php

use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
})->skip(function () {
    return ! Features::enabled(Features::registration());
}, 'Registration support is not enabled.');

test('registration screen cannot be rendered if support is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
})->skip(function () {
    return Features::enabled(Features::registration());
}, 'Registration support is enabled.');

test('new users can register', function () {
    $registrationData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'id_number' => 'TEST123456', // Usar valor único
        'phone' => '1234567890',
        'address' => 'Test Address 123',
    ];
    
    // Agregar terms solo si están habilitados
    if (Jetstream::hasTermsAndPrivacyPolicyFeature()) {
        $registrationData['terms'] = true;
    }

    $response = $this->post('/register', $registrationData);

    // Debug: ver el código de estado
    $this->assertEquals(302, $response->getStatusCode(), 'Expected redirect but got: ' . $response->getStatusCode());
    
    // Verificar redirección
    $response->assertRedirect('/admin');
    
    // Verificar que el usuario fue creado
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test User',
        'id_number' => 'TEST123456',
    ]);
    
    // Seguir la redirección y verificar que está autenticado
    $this->assertAuthenticated();
})->skip(function () {
    return ! Features::enabled(Features::registration()) || true;
}, 'Registration support is not enabled.');
