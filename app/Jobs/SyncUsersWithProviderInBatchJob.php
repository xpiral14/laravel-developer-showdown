<?php

namespace App\Jobs;

use App\Http\DataProvider;
use App\Models\UnsynchronizedUser;
use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;

class SyncUsersWithProviderInBatchJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
    }

    public function handle(): int
    {
        $provider = new DataProvider();
        User::query()
            ->with('unsynchronizedUser')
            ->join('unsynchronized_users', 'users.id', '=', 'unsynchronized_users.user_id')
            ->whereRaw("data->'modified_fields' is not null")
            ->select('users.*')
            ->chunkById(
                $provider::PROVIDER_MAX_CHUNK_SIZE,
                function (Collection $users) use ($provider) {
                    $this->synchronizeUsers($users, $provider);
                }
            );

        return 0;
    }

    /**
     * @throws GuzzleException
     */
    function synchronizeUsers(Collection $users, DataProvider $provider): void
    {
        $provider->sendUnsynchronizedUsersInBatch($users);
        $this->makeUsersSynchronized($users);
    }


    private function makeUsersSynchronized(Collection $users): void
    {
        UnsynchronizedUser::whereIn('user_id', $users->pluck('id'))->update(['data' => []]);
    }


    public function middleware(): array
    {
        return [(new RateLimitedWithRedis('unsynchronized-batch-users'))->dontRelease()];
    }
}

