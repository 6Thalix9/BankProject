<?php

namespace Database\Factories;

use App\Models\Transition;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transition>
 */
class TransitionFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Transition::class;
    
    
    
    
    
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        $accountsİDs = Account::pluck('id')->toArray();
        return [
            'sender_id' => $this->faker->randomElement($accountsİDs),
            'receiver_id' => $this->faker->randomElement($accountsİDs),
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'description' => $this->faker->text(100),

        ];
    }
}