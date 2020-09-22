<?php

namespace Porteiro\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel.
 *
 * @package Console
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\PhotoPorteiro\ChangeUserPassword::class,
        // Commands\PhotoPorteiro\User::class,
        // Commands\PhotoPorteiro\CreateRoles::class,
        // Commands\PhotoPorteiro\DeleteDetachedPhotosOlderThanWeek::class,
        // Commands\PhotoPorteiro\DeleteUnusedObjectsFromPhotoStorage::class,
        // Commands\PhotoPorteiro\GeneratePhotosMetadata::class,
        // Commands\PhotoPorteiro\GenerateRestApiDocumentation::class,
        // Commands\PhotoPorteiro\SendWeeklySubscriptionMails::class,
        // Commands\PhotoPorteiro\TestScheduler::class,

        // Commands\Photoacompanhante::class,


        // \Laravel\Tinker\Console\TinkerCommand::class,

        // /**
        //  * Me
        //  */
        // Commands\Explorer\InstagramGetAll::class,
        // Commands\Import\Data::class,
        // Commands\Import\Social::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //     $schedule->command('horizon:snapshot')->everyFiveMinutes();


        //     $schedule->command(Commands\PhotoPorteiro\TestScheduler::class)
        //         ->hourly();

        //     $schedule->command(Commands\PhotoPorteiro\DeleteDetachedPhotosOlderThanWeek::class)
        //         ->dailyAt('00:00')
        //         ->onOneServer();

        //     $schedule->command(Commands\PhotoPorteiro\DeleteUnusedObjectsFromPhotoStorage::class)
        //         ->dailyAt('00:10')
        //         ->onOneServer();

        //     $schedule->command(Commands\PhotoPorteiro\SendWeeklySubscriptionMails::class)
        //         ->weekly()
        //         ->sundays()
        //         ->at('06:00')
        //         ->onOneServer();


        //     $schedule->command('import:photoacompanhante')
        //         ->hourly();



        //     $schedule->command(Commands\Tools\PhotoApp\TestScheduler::class)
        //     ->hourly();

        // $schedule->command(Commands\Tools\PhotoApp\DeleteDetachedPhotosOlderThanWeek::class)
        //     ->dailyAt('00:00')
        //     ->onOneServer();

        // $schedule->command(Commands\Tools\PhotoApp\DeleteUnusedObjectsFromPhotoStorage::class)
        //     ->dailyAt('00:10')
        //     ->onOneServer();

        // $schedule->command(Commands\Tools\PhotoApp\SendWeeklySubscriptionMails::class)
        //     ->weekly()
        //     ->sundays()
        //     ->at('06:00')
        //     ->onOneServer();

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // include base_path('routes/console.php');
    }
}
