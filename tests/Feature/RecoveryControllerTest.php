<?php

namespace Tests\Feature;

use App\Mail\RecoveryMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RecoveryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testMissingEmailCredentialReturnsError()
    {
        Mail::fake();
        $this->putJson('api/reset-pwd', [])
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The email field is required.",
                "errors" => [
                    "email" => [
                        "The email field is required."
                    ]
                ]
            ]);
        Mail::assertNothingSent();
    }

    public function testMissingEmailReturnsError()
    {
        Mail::fake();
        $this->putJson('api/reset-pwd', [
            'email' => 'invalid@example.com'
        ])->assertUnprocessable()
            ->assertExactJson([
                "message" => "The selected email is invalid.",
                "errors" => [
                    "email" => [
                        "The selected email is invalid."
                    ]
                ]
            ]);
        Mail::assertNothingSent();
    }

    public function testPasswordHasChanged()
    {
        Mail::fake();
        $user = User::factory()->create();
        $originalEncryptedPassword = $user->password;
        $this->putJson('api/reset-pwd', [
            'email' => $user->email
        ]);
        $user->refresh();
        $this->assertNotEquals($user->password, $originalEncryptedPassword);
        Mail::assertSent(RecoveryMail::class);
    }

    public function testAuthenticatedUserIsRedirected()
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->putJson('api/reset-pwd', [
                'email' => $user->email
            ])->assertRedirect('/home');
    }
}
