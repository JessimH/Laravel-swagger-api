<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TasksTest extends TestCase
{

    public function createUser(){
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make($this->faker->password(8)),
        ];

        return $user = User::create($data);
    }

    // SHOW

    public function test_success_show_tasks(){
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $response = $this->actingAs($user)->getJson('/api/tasks');
        $response->assertStatus(200);
    }

    public function test_show_tasks_unauthorized(){
        $response = $this->postJson('/api/tasks');

        $response->assertStatus(405);
    }
    
    public function test_show_tasks_without_login(){
        $response = $this->getJson('/api/tasks');
        $response->assertStatus(401);
    }

    // CREATE

    public function test_success_create_task(){
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
            'body' => $this->faker->text,
            'user_id'=> $user->id
        ];

        $response = $this->actingAs($user)->postJson('/api/addTask', $postForm);
        $response->assertStatus(201);
    }

    public function test_create_task_without_login(){

        $postForm = [
            'body' => $this->faker->text
        ];

        $response = $this->postJson('/api/addTask', $postForm);
        $response->assertStatus(401);
    }

    public function test_create_task_without_input(){

        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
  
        ];

        $response = $this->actingAs($user)->postJson('/api/addTask', $postForm);
        $response->assertStatus(422);
    }

    // EDIT

    public function test__success_edit_task(){
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
            'id' => 1,
            'body' => $this->faker->text,
            'user_id'=> $user->id
        ];
        

        $response = $this->actingAs($user)->postJson('/api/addTask', $postForm);
        $editForm = [
            'body' => $this->faker->text,
        ];
        
        $response = $this->actingAs($user)->putJson('/api/updateTask/1', $editForm);
        
        $response->assertStatus(200);
    }

    public function test_edit_task_without_login(){
        $editForm = [
            'body' => $this->faker->text,
        ];
        $response = $this->putJson('/api/updateTask/1', $editForm);
        
        $response->assertStatus(401);
    }

    public function test_edit_task_wrong_user(){
        $user = $this->createUser();
        $user2 = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
            'id' => 1,
            'body' => $this->faker->text,
            'user_id'=> $user2->id
        ];

        $response = $this->actingAs($user2)->postJson('/api/addTask', $postForm);

        $editForm = [
            'body' => $this->faker->text,
        ];

        $response = $this->actingAs($user)->putJson('/api/updateTask/1', $editForm);
        
        $response->assertStatus(403);
    }

    public function test_edit_task_not_found(){
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $editForm = [
            'body' => $this->faker->text,
        ];
        $response = $this->actingAs($user)->putJson('/api/updateTask/99', $editForm);
        
        $response->assertStatus(404);
    }

    // CHECK TASK

     public function test_success_check_task(){
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
            'id' => 1,
            'body' => $this->faker->text,
            'user_id'=> $user->id
        ];

        $response = $this->actingAs($user)->postJson('/api/addTask', $postForm);

        $response = $this->actingAs($user)->getJson('/api/checkTask/1');
        
        $response->assertStatus(200);
    }

    // DELETE

    public function test_success_delete_task(){
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
            'id' => 1,
            'body' => $this->faker->text,
            'user_id'=> $user->id
        ];

        $response = $this->actingAs($user)->postJson('/api/addTask', $postForm);
        $response = $this->actingAs($user)->deleteJson('/api/deleteTask/1');
        
        $response->assertStatus(200);
    }

    public function test_delete_without_login(){

        $response = $this->deleteJson('/api/deleteTask/1');
        
        $response->assertStatus(401);
    }
    
    public function test_delete_task_wrong_user(){
        $user = $this->createUser();
        $user2 = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $postForm = [
            'id' => 1,
            'body' => $this->faker->text,
            'user_id'=> 13
        ];

        $response = $this->actingAs($user2)->postJson('/api/addTask', $postForm);
        $response = $this->actingAs($user)->deleteJson('/api/deleteTask/1');
        
        $response->assertStatus(403);
    }

    public function test_delete_task_not_found(){
        $user = $this->createUser();
        $token = $user->createToken('ios')->plainTextToken;

        $response = $this->actingAs($user)->deleteJson('/api/deleteTask/123456789123456789');
        
        $response->assertStatus(404);
    }
}
