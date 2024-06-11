<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\User;
use App\Models\Surah;
use App\Models\Verse;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{

    protected $model = Card::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'card_name' => Str::random(8),
            'due_at' => $this->faker->dateTimeThisMonth(),
            'is_performed' => '1',
            'usr_id' => User::all()->random()->id,
            'state_id' => $this->faker->numberBetween(1,6),
            'surah_id' => Surah::all()->random()->surah_id,
            'verse_id' => Verse::all()->random()->verse_id,
            'deck_id' => '1',
            'created_at' => $this->faker->dateTimeThisMonth(),
            
        ];
    }
}
