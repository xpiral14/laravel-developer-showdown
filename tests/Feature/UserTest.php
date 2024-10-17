<?php

namespace Tests\Feature;

use App\Jobs\SyncUsersWithProviderInBatchJob;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;


    public function test_if_can_update_user_with_name_samantha()
    {
        $lastName     = fake()->lastName();
        $timezone     = fake()->timezone();
        $user         = User::create(['firstname' => 'Samantha', 'lastname' => $lastName, 'time_zone' => $timezone, 'email' => fake()->email()]);
        $modifiedData = $user->unsynchronizedUser->data['modified_fields'];

        self::assertEquals('Samantha', $modifiedData['firstname']);
        self::assertEquals($lastName, $modifiedData['lastname']);
        self::assertEquals($timezone, $modifiedData['time_zone']);

        $job = new SyncUsersWithProviderInBatchJob();
        $job->handle();
        $unsynchronizedUser = $user->unsynchronizedUser()->first();


        self::assertArrayNotHasKey('modified_fields', $unsynchronizedUser->data);
    }
}
