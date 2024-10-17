<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $user->unsynchronizedUser()->create([
                                                'data' => [
                                                    'modified_fields' => $user->only(['firstname', 'lastname', 'time_zone'])
                                                ]
                                            ]);
    }

    public function updated(User $user): void
    {
        if (!$user->isDirty()) return;

        $user->unsynchronizedUser()->update([
                                                'data' => ['modified_fields' => \Arr::only($user->getDirty(), ['time_zone', 'firstname', 'lastname'])]
                                            ]);

    }
}
