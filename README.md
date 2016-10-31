# laravel-cache-rethinkdb
RethinkDB Cache Driver for Laravel 5.
This package makes it easy to store cached data in RethinkDB.

This way you can also view them in real time.

## TODO
- More tests
- Make a Query Builder, Eloquent for RethinkDB

## Setup
Install RethinkDB. 
- brew install rethinkdb or
- install Kitematic, search for rethinkdb and install the docker image.

## Installation

You can install the package via composer:

```bash
composer require demian/laravel-cache-rethinkdb
```

In your config/app.php
```bash
'providers' => [
    ...
    Demian\RethinkDB\RethinkDBServiceProvider::class,
];
```

In your config/cache.php, create a new store:
```bash
'rethinkdb' => [
    'driver' => 'rethinkdb',
    'connection' => [
        'host' => env('RETHINKDB_HOST', '192.168.99.100'),
        'port' => env('RETHINKDB_PORT', '28015'),
        'database' => env('RETHINKDB_DATABASE', 'forge'),
        'table' => 'cache'
    ]
]
```

Do not forget to create the table

## Usage

```php
Cache::store('rethinkdb')->get('key_1');
Cache::store('rethinkdb')->put('key_1', 1);
Cache::store('rethinkdb')->increment('rest_1', 1);
Cache::store('rethinkdb')->decrement('rest_1', 1);
```

See: https://laravel.com/docs/5.3/cache

See: https://www.rethinkdb.com
