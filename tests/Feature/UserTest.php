<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

use Laravel\Sanctum\Sanctum;

class UserTest extends TestCase
{
/*     use DatabaseMigrations;
    use RefreshDatabase; */

    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function test_register_withoutCredentials()
    {
 /*        $user = Sanctum::actingAs(User::factory()->create());
        dd($user); */

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/register', []);
        $response->assertStatus(422);
    }

    public function test_register_Successful()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/register', [
            'name' => 'test',
            'email' => 'test@mail.com',
            'password' => 'password'
        ]);
        $response->assertStatus(201);
        $response->assertExactJson([
            'message' => 'sucessful register',
            ]);
    }


    public function test_register_UniqueEmail()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/register', [
            'email' => 'test@mail.com',
            'password' => 'password'
        ]);
        $response->assertStatus(422);

    }

    public function test_register_UnvalidEmail()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/register', [
            'email' => 'test@mail',
            'password' => 'password'
        ]);
        $response->assertStatus(422);
    }


    public function test_login_success()
    {
      //  \App\Models\User::factory()->create();

        $user = User::create([
            'email' => 'sample@test.com',
            "name" => 'test',
            'password' => bcrypt('sample123'),
         ]);
 
         $loginData = ['email' => 'sample@test.com', 'password' => 'sample123'];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login', $loginData);

        $response->assertStatus(200);
    }

    public function test_login_badCredentials()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login', [
            'email' => 'test@mail.fr',
            'password' => 'pass'
        ]);

        $response->assertStatus(404);
    }

    public function test_login_withoutCredentials()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/login', []);
        $response->assertStatus(422);
    }

    public function test_get_tasks_without_token()
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->get('/api/tasks', []);
        $response->assertStatus(401);
    }

    public function test_get_tasks()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('my-app-token')->plainTextToken;
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->get('/api/tasks', []);
        $response->assertStatus(200);
    }

    public function test_get_tasks_completed()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('my-app-token')->plainTextToken;
        $task = Task::create(['body'=>"test", "completed" => 1, 'user_id' => $user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->get('/api/tasks?completed=true', []);
        $response->assertStatus(200);
    }



    public function test_create_task()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('my-app-token')->plainTextToken;
        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->post('/api/tasks', [
            'body' => 'test',
        ]);

        $response->assertStatus(200);
    }


    public function test_create_task_with_error()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('my-app-token')->plainTextToken;

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->post('/api/tasks', []);
        $response->assertStatus(422);

    }

    public function test_get_task_with_id()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('my-app-token')->plainTextToken;
        $task = Task::create(['body'=>"test", "completed" => 0, 'user_id' => $user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token

        ])->get('/api/tasks/'.$task->id, []);

        $response->assertStatus(200);
    }

    public function test_get_task_without_permission()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('my-app-token')->plainTextToken;

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token

        ])->get('/api/tasks/1', []);

        $response->assertStatus(403);
    }


    public function test_get_task_with_id_no_exist()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('my-app-token')->plainTextToken;

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token

        ])->get('/api/tasks/999', []);

        $response->assertStatus(404);
    }

    public function test_delete_task_with_id()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('my-app-token')->plainTextToken;
        $task = Task::create(['body'=>"test", "completed" => 0, 'user_id' => $user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->delete('/api/tasks/'.$task->id, []);
        $response->assertStatus(200);
    }

    public function test_delete_task_without_permission()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('my-app-token')->plainTextToken;

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->delete('/api/tasks/1', []);
        $response->assertStatus(403);
    }


    public function test_update_task()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('my-app-token')->plainTextToken;
        $task = Task::create(['body'=>"test", "completed" => 0, 'user_id' => $user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->put('/api/tasks/'.$task->id, ['body'=>"teszet", "completed" => 0, 'user_id' => $user->id]);

        $response->assertStatus(200);
    }

    public function test_update_task_without_permission()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('my-app-token')->plainTextToken;

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->put('/api/tasks/1', ['body'=>"teszet", "completed" => 0, 'user_id' => $user->id]);

        $response->assertStatus(403);
    }

    public function test_update_task_with_errors()
    {
        $user  = User::factory()->create();
        $token = $user->createToken('my-app-token')->plainTextToken;
        $task = Task::create(['body'=>"test", "completed" => 0, 'user_id' => $user->id]);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->put('/api/tasks/'.$task->id, [ "completed" => 0, 'user_id' => $user->id]);

        $response->assertStatus(422);
    }
}
