<?php

namespace App\Console\Commands\UnpaidRecovery;

use Illuminate\Console\Command;
use App\Models\UnpaidRecovery;
use Carbon\Carbon;
use App\Models\Customer;
use App\Notifications\UnpaidRecovery\SecondRelaunch;

class RelaunchCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unpaid:recovery:relaunch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $unpaidRecovery = UnpaidRecovery::where('next_relaunch', '<', Carbon::now())->where('process', '1')->where('status', 'in_progress')->where('locked', 'false');
        if ($unpaidRecovery->count() != '0') {
            $info = $unpaidRecovery->first();
            UnpaidRecovery::where('id', $info->id)->update([
                'last_relaunch' => now(),
                'next_relaunch' => Carbon::now()->addDays(7),
                'process' => '2',
                'locked' => 'true',
            ]);
            $user = Customer::find($info->customerid);
            $user->notify(new SecondRelaunch($info));
        }
    }
}
