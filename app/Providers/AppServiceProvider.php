<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*',function($view) {
            $theme = DB::table('themes');

            if(isset(auth()->user()->role)){
                $theme = $theme->where('admin_id', auth()->user()->admin_id ?? auth()->user()->id);
                $view->with('theme', $theme->pluck('value', 'name'));
            }
            elseif(isset($_GET['u'])){
                $theme = $theme->where('admin_id', $_GET['u']);
                $view->with('theme', $theme->pluck('value', 'name'));
            }
        });
    }
}
