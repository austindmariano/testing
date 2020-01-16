<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class SystemSettingsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //get and set current academic year and semester
        $settings = \DB::table('settings')->first();
        Config::set('settings.current_ay', $settings->current_academic_year);
        Config::set('settings.current_sem', $settings->current_semester);

        //get all activities
        $activities = \DB::table('user_activities')->get();
        //record id of all activities to config (settings.php)
        foreach ($activities as $key => $value) {
            Config::set('settings.' . $value->title, $value->id);
        }
    }
}
