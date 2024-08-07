<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Bank;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    
     /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */    public function definition(): array
    {
        $user = User::inRandomOrder()->first();
        

        if(!$user){
            $user = User::factory()->create();
        }
        return [
            'user_id' => $user->id,
            'account_number' => $this->faker->unique()->bankAccountNumber,
            'balance' => $this->faker->numberBetween(1000,10000),
            

        ];
    }
}