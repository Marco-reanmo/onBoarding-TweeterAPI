<?php

namespace Tests\Feature;

use App\Jobs\SendVerificationEmail;
use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    const selectedSymbols = [
        ',', ';', ':', '_', '#', '+', '*', '~', '´', '`', '?', '\\', '/', '=',
        '[', '(', '{', '}', ')', ']', '&', '%', '$', '§', '"', '!', '°', '^', '<', '>', '|'
    ];
    const letters = [
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        'ß'
    ];

    private array $payload;
    private Closure $passwordValidator;

    public function setUp(): void
    {
        parent::setUp();
        $this->passwordValidator = function ($password) {
            return ($this->hasCorrectLength($password) &&
                Str::contains($password, self::letters) &&
                Str::contains($password, range(0, 9)) &&
                Str::contains($password, self::selectedSymbols));
        };
        $password = $this->faker->valid($this->passwordValidator)->password();
        $this->payload = User::factory()
            ->make([
                'password' => $password,
            ])->getAttributes();
        $this->payload['password_confirmation'] = $password;
        $file = UploadedFile::fake()->image('profile_picture.jpg');
        $this->payload['profile_picture'] = $file;
        unset($this->payload['email_verified_at']);
        unset($this->payload['remember_token']);
    }

    private function hasCorrectLength($password, $min = 8, $max = 64): bool
    {
        return in_array(Str::length($password), range($min, $max));
    }

    public function testMissingForenameReturnsError()
    {
        unset($this->payload['forename']);
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The forename field is required.",
                "errors" => [
                    "forename" => [
                        "The forename field is required."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testForenameThatIsTooLongReturnsError()
    {
        $this->payload['forename'] = Str::repeat($this->faker->randomLetter(), 256);
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The forename must not be greater than 255 characters.",
                "errors" => [
                    "forename" => [
                        "The forename must not be greater than 255 characters."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testForenameWithNumberReturnsError()
    {
        $this->payload['forename'] .= $this->faker->randomDigit();
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The forename must only contain letters, dots, hyphens or apostrophes.",
                "errors" => [
                    "forename" => [
                        "The forename must only contain letters, dots, hyphens or apostrophes."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testForenameWithSymbolReturnsError()
    {
        $this->payload['forename'] .= $this->faker->randomElement(self::selectedSymbols);
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The forename must only contain letters, dots, hyphens or apostrophes.",
                "errors" => [
                    "forename" => [
                        "The forename must only contain letters, dots, hyphens or apostrophes."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testMissingSurnameReturnsError()
    {
        unset($this->payload['surname']);
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The surname field is required.",
                "errors" => [
                    "surname" => [
                        "The surname field is required."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testSurnameThatIsTooLongReturnsError()
    {
        $this->payload['surname'] = Str::repeat($this->faker->randomLetter(), 256);
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The surname must not be greater than 255 characters.",
                "errors" => [
                    "surname" => [
                        "The surname must not be greater than 255 characters."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testSurnameWithNumberReturnsError()
    {
        $this->payload['surname'] .= $this->faker->randomDigit();
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The surname must only contain letters, dots, hyphens or apostrophes.",
                "errors" => [
                    "surname" => [
                        "The surname must only contain letters, dots, hyphens or apostrophes."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testSurnameWithSymbolReturnsError()
    {
        $this->payload['surname'] .= $this->faker->randomElement(self::selectedSymbols);
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The surname must only contain letters, dots, hyphens or apostrophes.",
                "errors" => [
                    "surname" => [
                        "The surname must only contain letters, dots, hyphens or apostrophes."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testProfilePictureIsOptional()
    {
        unset($this->payload['profile_picture']);
        $response = $this->postJson('api/register', $this->payload)
            ->assertCreated();
        $this->assertInstanceOf(Model::class, $response->getOriginalContent());
        $response->assertJsonStructure(['user']);
        $this->assertDatabaseCount('users', 1);
    }

    public function testProfilePictureThatIsNoImageFileReturnsError()
    {
        $this->payload['profile_picture'] = $this->faker->randomLetter();
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                'message' => 'The profile picture must be an image.',
                'errors' => [
                    'profile_picture' => [
                        'The profile picture must be an image.'
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testMissingEmailReturnsError()
    {
        unset($this->payload['email']);
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The email field is required.",
                "errors" => [
                    "email" => [
                        "The email field is required."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testEmailThatIsTooLongReturnsError()
    {
        $suffix = '@example.com';
        $this->payload['email'] = Str::repeat($this->faker->randomLetter(), 256 - Str::length($suffix)) . $suffix;
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The email must not be greater than 255 characters.",
                "errors" => [
                    "email" => [
                        "The email must not be greater than 255 characters."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testEmailThatIsNotAnEmailReturnsError()
    {
        $this->payload['email'] = $this->faker->randomLetter();
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                'message' => 'The email must be a valid email address.',
                'errors' => [
                    'email' => [
                        'The email must be a valid email address.'
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testEmailThatIsAlreadyTakenReturnsError()
    {
        $anotherUser = User::factory()->create();
        $this->payload['email'] = $anotherUser->email;
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                'message' => 'The email has already been taken.',
                'errors' => [
                    'email' => [
                        'The email has already been taken.'
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 1);
    }

    public function testMissingPasswordReturnsError()
    {
        unset($this->payload['password']);
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The password field is required.",
                "errors" => [
                    "password" => [
                        "The password field is required."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testMissingConformationReturnsError()
    {
        unset($this->payload['password_confirmation']);
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The password confirmation does not match.",
                "errors" => [
                    "password" => [
                        "The password confirmation does not match."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testWrongConformationReturnsError()
    {
        $this->payload['password_confirmation'] = $this->faker->valid($this->passwordValidator)->password();
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The password confirmation does not match.",
                "errors" => [
                    "password" => [
                        "The password confirmation does not match."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testPasswordThatIsTooShortReturnsError()
    {
        $customValidator = function ($password) {
            return ($this->hasCorrectLength($password, 1, 7) &&
                Str::contains($password, self::letters) &&
                Str::contains($password, range(0, 9)) &&
                Str::contains($password, self::selectedSymbols));
        };
        $tooShortPassword = $this->faker->valid($customValidator)->password();
        $this->payload['password'] = $tooShortPassword;
        $this->payload['password_confirmation'] = $tooShortPassword;
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The password must be at least 8 characters.",
                "errors" => [
                    "password" => [
                        "The password must be at least 8 characters."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testPasswordThatIsTooLongReturnsError()
    {
        $customValidator = function ($password) {
            return ($this->hasCorrectLength($password, 65, 128) &&
                Str::contains($password, range(0, 9)) &&
                Str::contains($password, self::selectedSymbols));
        };
        $tooLongPassword = $this->faker->valid($customValidator)->password(65);
        $this->payload['password'] = $tooLongPassword;
        $this->payload['password_confirmation'] = $tooLongPassword;
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The password must not be greater than 64 characters.",
                "errors" => [
                    "password" => [
                        "The password must not be greater than 64 characters."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testPasswordThatDoesNotContainLetterReturnsError()
    {
        $passwordWithoutLetters = $this->faker->numberBetween(1000000, 9999999)
            . $this->faker->randomElement(self::selectedSymbols);
        $this->payload['password'] = $passwordWithoutLetters;
        $this->payload['password_confirmation'] = $passwordWithoutLetters;
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The password must contain at least one letter.",
                "errors" => [
                    "password" => [
                        "The password must contain at least one letter."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testPasswordThatDoesNotContainNumberReturnsError()
    {
        $passwordWithoutNumber = Str::repeat($this->faker->randomLetter(), 7)
            . $this->faker->randomElement(self::selectedSymbols);
        $this->payload['password'] = $passwordWithoutNumber;
        $this->payload['password_confirmation'] = $passwordWithoutNumber;
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The password must contain at least one number.",
                "errors" => [
                    "password" => [
                        "The password must contain at least one number."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testPasswordThatDoesNotContainSymbolReturnsError()
    {
        $passwordWithoutSymbol = Str::repeat($this->faker->randomLetter(), 7)
            . $this->faker->randomDigit();
        $this->payload['password'] = $passwordWithoutSymbol;
        $this->payload['password_confirmation'] = $passwordWithoutSymbol;
        $this->postJson('api/register', $this->payload)
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The password must contain at least one symbol.",
                "errors" => [
                    "password" => [
                        "The password must contain at least one symbol."
                    ]
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testRegisterRequestValidationDoesNotStopOnFirstFailure()
    {
        $this->postJson('api/register', [])
            ->assertUnprocessable()
            ->assertExactJson([
                "message" => "The forename field is required. (and 3 more errors)",
                "errors" => [
                    "email" => [
                        "The email field is required."
                    ],
                    "forename" => [
                        "The forename field is required."
                    ],
                    "surname" => [
                        "The surname field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                ]
            ]);
        $this->assertDatabaseCount('users', 0);
    }

    public function testRegisterReturnsInstanceOfUserModel()
    {
        $response = $this->postJson('api/register', $this->payload)
            ->assertCreated();
        $this->assertInstanceOf(Model::class, $response->getOriginalContent());
        $response->assertJsonStructure(['user']);
    }

    public function testUserHasBeenCreatedSuccessfully()
    {
        $this->postJson('api/register', $this->payload)
            ->assertCreated();
        $this->assertDatabaseHas('users', [
            'forename' => $this->payload['forename'],
            'surname' => $this->payload['surname'],
            'email' => $this->payload['email'],
        ]);
    }

    public function testProfilePictureIsStoredCorrectly()
    {
        $this->postJson('api/register', $this->payload)
            ->assertCreated();
        $userFromDB = User::query()->email($this->payload['email'])->first();
        $this->assertDatabaseHas('images', [
            'imageable_id' => $userFromDB->id,
            'imageable_type' => 'App\\Models\\User',
            'image' => file_get_contents($this->payload['profile_picture'])
        ]);
    }

    public function testPasswordIsHashedCorrectly()
    {
        $this->postJson('api/register', $this->payload)
            ->assertCreated();
        $userFromDB = User::query()->email($this->payload['email'])->first();
        $this->assertTrue(Hash::check($this->payload['password'], $userFromDB->password));
    }

    public function testVerificationEmailJobIsCreated()
    {
        Queue::fake();
        $this->postJson('api/register', $this->payload)
            ->assertCreated();
        Queue::assertPushed(SendVerificationEmail::class);
    }

    public function testAuthenticateUserIsRedirected()
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->postJson('api/register', $this->payload)
            ->assertRedirect('/home');
    }
}
