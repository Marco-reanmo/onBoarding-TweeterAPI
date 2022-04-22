<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $email;
    private string $password;
    private array $payload;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->email = $this->user->email;
        $this->password = 'password';
        $this->payload = [
          'email' => $this->email,
          'password' => $this->password
        ];
    }

    public function testUserWithValidCredentialsIsLoggedInSuccessfully()
    {
        $this->postJson('api/login', $this->payload)
            ->assertOk();
        $this->assertAuthenticatedAs($this->user);
    }

    public function testSuccessfulLoginReturnsUserModel()
    {
        $response = $this->postJson('api/login', $this->payload)
            ->assertOk();
        $this->assertInstanceOf(Model::class, $response->getOriginalContent());
        $response->assertJsonStructure(['user']);
    }

    public function testUnregisteredEmailReturnsForbidden()
    {
        $this->postJson('api/login', [
            'email' => 'invalid@example.com',
            'password' => $this->password
            ]
        )->assertForbidden();
        $this->assertGuest();
    }

    public function testMissingEmailCredentialReturnsException()
    {
        $this->postJson('api/login', ['password' => $this->password])
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The email field is required.",
                "errors" => [
                    "email" => [
                        "The email field is required."
                    ]
                ]
            ]);
        $this->assertGuest();
    }

    public function testEmailCredentialThatIsNotAnEmailReturnsException()
    {
        $this->postJson('api/login', [
            'email' => 'invalid',
            'password' => $this->password
            ]
        )->assertUnprocessable()
            ->assertExactJson([
                "message" => "The email must be a valid email address.",
                "errors" => [
                    "email" => [
                        "The email must be a valid email address."
                    ]
                ]
            ]);
        $this->assertGuest();
    }

    public function testWrongPasswordReturnsForbidden()
    {
        $this->postJson('api/login', [
                'email' => $this->user->email,
                'password' => 'invalid'
            ]
        )->assertForbidden();
        $this->assertGuest();
    }

    public function testMissingPasswordCredentialReturnsException()
    {
        $this->postJson('api/login', [
            'email' => $this->user->email
            ]
        )->assertUnprocessable()
            ->assertExactJson([
                "message" => "The password field is required.",
                "errors" => [
                    "password" => [
                        "The password field is required."
                    ]
                ]
            ]);
        $this->assertGuest();
    }

    public function testValidationStopsOnFirstError()
    {
        $this->postJson('api/login', [])
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The email field is required.",
                "errors" => [
                    "email" => [
                        "The email field is required."
                    ]
                ]
            ]);
        $this->assertGuest();
    }

    public function testAlreadyAuthenticatedUserCannotLoginTwiceAndWillBeRedirectedToHome()
    {
        $this->actingAs($this->user)
            ->postJson('api/login', $this->payload)
            ->assertRedirect('/home');
    }
}
