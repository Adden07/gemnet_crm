<?php

namespace App\Console;

use App\Traits\CronJobs;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    use CronJobs;
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $this->expire_marketplace_quotes();
        })->everyTwoHours();

        $schedule->call(function () {
            $this->expire_shipments();
        })->everyTwoHours();

        $schedule->call(function () {
            $this->expire_ltl_quotes();
        })->everyTwoHours();

        $schedule->call(function () {
            $this->cancel_shipments();
        })->everyTwoHours();

        $schedule->call(function () {
            $this->making_feedback_public();
        })->daily()->at('12:00');
        
        $schedule->call(function () {
            $this->making_shipments_marked_as_delivered_by_shipper();
        })->daily()->at('12:00');

        $schedule->call(function () {
            $this->making_dummy_for_yopmail_users();
        })->daily()->at('12:10');

        $schedule->command('backup:clean --disable-notifications')->daily()->at('01:00');
        $schedule->command('backup:run --only-db --disable-notifications')->daily()->at('01:30');
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
