<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use App\Models\User;

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
                $theme = $theme->where('admin_id', auth()->user()->admin_id ?? auth()->user()->id)->pluck('value', 'name');
                if(!$theme->count()){
                    $user = User::find($_GET['u']);
                    $theme = DB::table('themes')->where('admin_id', $user->admin_id)->pluck('value', 'name');
                }
                $view->with('theme', $theme);
            }
            elseif(isset($_GET['u'])){
                $theme = $theme->where('admin_id', $_GET['u'])->pluck('value', 'name');
                if(!$theme->count()){
                    $user = User::find($_GET['u']);
                    $theme = DB::table('themes')->where('admin_id', $user->admin_id)->pluck('value', 'name');
                }
                $view->with('theme', $theme);
            }
        });
    }
}
