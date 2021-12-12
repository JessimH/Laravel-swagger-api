<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_success_login(){

        $password = $this->faker->password(8);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make($password),
        ];

        $user = User::create($data);

        $form = [
            'email' => $user->email,
            'password' => $password,
            'device_name' => 'ios'
        ];

        $response = $this->postJson('/api/auth/login', $form);

        $this->assertDatabaseHas('users', $data);

        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    'token', 
                    'name', 
                    'email'
                ])
            ->assertJson([
                'email' => $user->email
            ]);
    }

    public function test_with_invalid_credentials(){

        $data = [
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
            'device_name' => 'ios'
        ];

        $response = $this->postJson('/api/auth/login', $data);
        $response->assertStatus(401)->assertJsonStructure([
            'message'
        ]);
    }

    public function test_with_invalid_input(){

        $data = [
            'email' => $this->faker->name(),
            'password' => $this->faker->password(),
            'device_name' => 'ios'
        ];

        $response = $this->postJson('/api/auth/login', $data);
        $response->assertStatus(401)->assertJsonStructure([
            'message'
        ]);
    }

    public function test_without_input(){

        $response = $this->postJson('/api/auth/login');
        $response->assertStatus(400)->assertJsonStructure([
            'message'
        ]);
    }
}
