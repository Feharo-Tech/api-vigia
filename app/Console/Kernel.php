<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\CheckApiStatusJob;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        //
    ];

    /**
     * Define os comandos do aplicativo.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }

    /**
     * Define o agendamento de comandos.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            CheckApiStatusJob::dispatch();
        })->everyMinute();
    }
}
