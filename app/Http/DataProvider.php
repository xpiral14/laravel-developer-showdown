<?php

namespace App\Http;


use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

class DataProvider
{

    const PROVIDER_MAX_CHUNK_SIZE = 1000;
    const MAX_UPDATE_USERS_PER_HOUR = 50000;
    private Client $httpClient;

    public function __construct()
    {

        $this->httpClient = new Client(['base_uri' => env('DATA_PROVIDER_URL'), 'http_errors' => false]);
    }

    public function sendUnsynchronizedUsersInBatch(Collection $users): void
    {
        foreach ($users as $user) {
            $userLog = collect($this->getUserPayload($user))->map(fn($value, $key) => "$key: $value")->join(', ');

            Log::driver('dataprovider')->info("[$user->id] $userLog");
        }
        $usersPayload = [
            'batches' => [
                'subscribers' =>
                    $users->map(fn(User $user) => $this->getUserPayload($user))->toArray()
            ]
        ];

    }

    function getUserPayload(User $user): array
    {
        return \Arr::only($user->unsynchronizedUser->data['modified_fields'], ['lastname', 'firstname', 'time_zone']);
    }
}
