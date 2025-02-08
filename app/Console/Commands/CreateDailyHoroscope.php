<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\HoroscopeController;
use Illuminate\Console\Command;

class CreateDailyHoroscope extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:daily-horoscope';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create:daily-horoscope';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        ini_set('max_execution_time', 480);

        $horoscope = new HoroscopeController();
        $horoscope->generateDailyHorscope(false);
        $horoscope->generateWeeklyHorscope(false);
        $horoscope->generateYearlyHorscope(false);
    }
}
