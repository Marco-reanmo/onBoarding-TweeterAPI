<?php

namespace Database\Factories;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{

    protected $binary;

    public function __construct($count = null, ?Collection $states = null, ?Collection $has = null, ?Collection $for = null, ?Collection $afterMaking = null, ?Collection $afterCreating = null, $connection = null)
    {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);
        $this->binary = file_get_contents('public/images/reanmo-bg-image.png');
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $imageable = $this->imageable();
        return [
            'image' => $this->binary,
            'imageable_id' => $imageable::factory(),
            'imageable_type' => $imageable
        ];
    }

    public function imageable()
    {
        return $this->faker->randomElement([
            User::class,
            Tweet::class
        ]);
    }
}
