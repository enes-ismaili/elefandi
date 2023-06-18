<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CheckVendorStatus::class,
        Commands\CheckMembership::class,
        Commands\CheckMembershipTwiceMonthly::class,
        Commands\GenerateRaports::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('vendorstatus:twice')->twiceDaily(1, 13);
        $schedule->command('membership:daily')->daily();
        $schedule->command('membership:twicemonthly')->twiceMonthly(1, 16, '00:10');
        $schedule->command('raport:generate')->lastDayOfMonth('23:59');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
