<?php namespace Demian\RethinkDB;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class RethinkDBServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Cache::extend('rethinkdb', function ($app) {
            $connection = Db::connection('rethinkdb');
            
            return Cache::repository(new RethinkDBStore($connection));
        });
    }
    
    public function register()
    {
        $this->app->singleton('db.connection.rethinkdb', function ($app, $parameters) {
            list($connection, $database, $prefix, $config) = $parameters;
    
            return new RethinkDBConnection($database, $prefix, $config);
        });
    }
}
