<?php

namespace App\Console;

use App\Jobs\Job;
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
        Commands\WarningMailSendToUser::class,
        //Commands\ReminderMailSendToUser::class,
        //Commands\AccountDeactivationMailSendToUser::class,
        //Commands\WarningMailSendToUserForDeleteAccount::class,
        Commands\SubscriptionExpireSchedule::class,
        //Commands\DatabaseBackup::class,
        Commands\DBBackupSchedule::class,
        Commands\SendTagReport::class,
        Commands\SendNormalImageTagReport::class,
        Commands\DeleteGeneratedVideoSchedule::class,
        Commands\GetTotalSignUpAndDesignOfPreviousDay::class,
        Commands\ToActivatePayPalSubscriptions::class,
        Commands\DeleteUnusedRecordFromDB::class,
        Commands\MoveMultiPageJsonDataToS3::class,
        Commands\SetContentIdsInStaticPage::class,
        Commands\WarningMailSendToUserV2::class,
        Commands\AccountDeletionMailSendToUser::class,
        Commands\SendDeactiveUserReportFileToAdmin::class,
        Commands\DeleteDesignJobDataCommand::class,
        //Commands\ExportDatabaseTables::class,
        //Commands\ImportDatabaseTables::class,
        Commands\DeleteGeneratedDesignFiles::class,
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
        //$schedule->command('WarningMailSendToUser:command')->everyMinute();
//        $schedule->command('WarningMailSendToUserV2')->hourly();
//        $schedule->command('AccountDeletionMailSendToUser')->daily();
//        $schedule->command('SendDeactiveUserReportFileToAdmin')->monthly();
        //$schedule->command('ReminderMailSendToUser:command')->daily();
        //$schedule->command('AccountDeactivationMailSendToUser:command')->daily();
        //$schedule->command('WarningMailSendToUserForAccountDeletion:command')->daily();

        $schedule->command('SubscriptionExpire:command')->dailyAt('3:30');//everyMinute();
        $schedule->command('sendreportmail')->weekly()->mondays()->at('4:00');
        $schedule->command('sendNormalImageReportMail')->weekly()->mondays()->at('4:00');
        $schedule->command('DeleteGeneratedVideo:command')->dailyAt('00:30'); //6 AM Ind
        $schedule->command('getTotalSignUpAndDesignOfPreviousDay')->dailyAt('4:30');  //10 AM Ind
        $schedule->command('ToActivatePayPalSubscriptions:command')->hourly(); //8 AM Ind
        $schedule->command('DeleteUnusedRecordFromDB')->daily();
        $schedule->command('MoveMultiPageJsonDataToS3')->daily();
        $schedule->command('delete-generated-design-files')->dailyAt('01:00');
        $schedule->command('delete-design-template-job-data')->daily();
        //$schedule->command('DatabaseBackup')->monthly();
        //$schedule->command('ExportDatabaseTables')->monthly();
        //$schedule->command('ImportDatabaseTables')->monthly()->at('1:00'); //6:30 AM Ind
        //$schedule->command('DBBackup:command')->dailyAt('9:10');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
