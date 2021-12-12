<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function test_success_register(){

        $password = $this->faker->password(8);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $form = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $password,
            'device_name' => 'ios',
        ];


        $response = $this->postJson('/api/auth/register', $form);

        $this->assertDatabaseHas('users', $data);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'name', 'email', 'created_at'])
            ->assertJson(['email' => $data['email'], 'name' => $data['name']]);
    }

    public function test_account_exist(){

        $form = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password(8),
            'device_name' => 'ios',
        ];

        $this->postJson('/api/auth/register', $form);

        $response = $this->postJson('/api/auth/register', $form);

        $response->assertStatus(409)->assertJsonStructure(['message']);
    }

    public function test_with_invalid_input(){
        $data = [
            'email' => $this->faker->name,
            'password' => $this->faker->password,
        ];

        $response = $this->postJson('/api/auth/register', $data);
        $response->assertStatus(400)->assertJsonStructure(['message']);
    }

    public function test_without_input(){
        $response = $this->postJson('/api/auth/register');
        $response->assertStatus(400)->assertJsonStructure(['message']);
    }
}
