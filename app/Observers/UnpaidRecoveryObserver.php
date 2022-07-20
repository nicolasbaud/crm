<?php

namespace App\Observers;

use App\Models\UnpaidRecovery;
use App\Models\Customer;
use App\Notifications\UnpaidRecovery\FirstRelaunch;
use Carbon\Carbon;

class UnpaidRecoveryObserver
{
    /**
     * Handle the UnpaidRecovery "created" event.
     *
     * @param  \App\Models\UnpaidRecovery  $unpaidRecovery
     * @return void
     */
    public function created(UnpaidRecovery $unpaidRecovery)
    {
        UnpaidRecovery::where('id', $unpaidRecovery->id)->update([
            'last_relaunch' => now(),
            'next_relaunch' => Carbon::now()->addDays(7),
            'process' => '1',
            'status' => 'in_progress',
        ]);
        $user = Customer::find($unpaidRecovery->customerid);
        $user->notify(new FirstRelaunch($unpaidRecovery));
    }

    /**
     * Handle the UnpaidRecovery "updated" event.
     *
     * @param  \App\Models\UnpaidRecovery  $unpaidRecovery
     * @return void
     */
    public function updated(UnpaidRecovery $unpaidRecovery)
    {
        //
    }

    /**
     * Handle the UnpaidRecovery "deleted" event.
     *
     * @param  \App\Models\UnpaidRecovery  $unpaidRecovery
     * @return void
     */
    public function deleted(UnpaidRecovery $unpaidRecovery)
    {
        //
    }

    /**
     * Handle the UnpaidRecovery "restored" event.
     *
     * @param  \App\Models\UnpaidRecovery  $unpaidRecovery
     * @return void
     */
    public function restored(UnpaidRecovery $unpaidRecovery)
    {
        //
    }

    /**
     * Handle the UnpaidRecovery "force deleted" event.
     *
     * @param  \App\Models\UnpaidRecovery  $unpaidRecovery
     * @return void
     */
    public function forceDeleted(UnpaidRecovery $unpaidRecovery)
    {
        //
    }
}
