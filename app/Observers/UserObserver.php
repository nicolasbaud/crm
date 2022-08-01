<?php

namespace App\Observers;

use App\Models\User;
use App\Models\ModelHasRole;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $User
     * @return void
     */
    public function created(User $user)
    {
        User::where('id', $user->id)->update([
            'role' => now(),
            'next_relaunch' => Carbon::now()->addDays(7),
            'process' => '1',
            'status' => 'in_progress',
        ]);
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $User
     * @return void
     */
    public function updated(User $User)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $User
     * @return void
     */
    public function deleted(User $User)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $User
     * @return void
     */
    public function restored(User $User)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $User
     * @return void
     */
    public function forceDeleted(User $User)
    {
        //
    }
}
