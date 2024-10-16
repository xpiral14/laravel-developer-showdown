<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdateUserWithRandomValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-user-attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        User::all()->each(function (User $user) {
            $user->update([
                            'firstname' => fake()->firstName(),
                            'lastname' => fake()->lastName(),
                            'time_zone' => fake()->timezone(),
                        ]);
        });
    }
}
