<?php namespace Demian\RethinkDB;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class RethinkDBServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Cache::extend('rethinkdb', function ($app, $config) {
            $config = $config['connection'];
            $connection = new RethinkDBConnection($config['host'], $config['port'], $config['database'], $config['table']);
            
            return Cache::repository(new RethinkDBStore($connection));
        });
    }
}
