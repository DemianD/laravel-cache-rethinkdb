<?php namespace Demian\RethinkDB;

use Carbon\Carbon;
use Illuminate\Contracts\Cache\Store;

class RethinkDBStore implements Store
{
    /**
     * The RethinkDB connection that should be used.
     *
     * @var string
     */
    protected $rethinkdb;
    
    /**
     * A string that should be prepended to keys.
     *
     * @var string
     */
    protected $prefix = '';
    
    /**
     * RethinkDBStore constructor.
     *
     * @param \Demian\RethinkDB\RethinkDBConnection $rethinkdb
     */
    public function __construct(RethinkDBConnection $rethinkdb)
    {
        $this->rethinkdb = $rethinkdb;
    }
    
    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string|array $key
     *
     * @return mixed
     */
    public function get($key)
    {
        $prefixed = $this->prefix.$key;
    
        $cache = $this->rethinkdb->query()->first(['key' => $prefixed]);
    
        if (! is_null($cache)) {
            if (is_array($cache)) {
                $cache = (object) $cache;
            }
            
            if (Carbon::now()->getTimestamp() >= $cache->expiration) {
                $this->forget($key);
            
                return;
            }
        
            return $cache->value;
        }
    }
    
    /**
     * Store an item in the cache for a given number of minutes.
     *
     * @param  string $key
     * @param  mixed $value
     * @param  float|int $minutes
     *
     * @return void
     */
    public function put($key, $value, $minutes = 60)
    {
        $key = $this->prefix.$key;
        
        $expiration = $this->getTime() + (int) ($minutes * 60);
        
        $item = $this->rethinkdb->query()->first(['key' => $key]);
            
        if (is_null($item)) {
            $this->rethinkdb->query()->create(compact('key', 'value', 'expiration'));
        } else {
            $item->expiration = $expiration;
            $item->value = $value;
            $this->rethinkdb->query()->update($item);
        }
    }
    
    /**
     * Increment the value of an item in the cache.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return int|bool
     */
    public function increment($key, $value = 1)
    {
        $this->rethinkdb->query()->incrementOrDecrement($key, $value);
    }
    
    /**
     * Decrement the value of an item in the cache.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return int|bool
     */
    public function decrement($key, $value = 1)
    {
        $this->rethinkdb->query()->incrementOrDecrement($key, -$value);
    }
    
    /**
     * Store an item in the cache indefinitely.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return void
     */
    public function forever($key, $value)
    {
        $this->put($key, $value, 5256000);
    }
    
    /**
     * Remove an item from the cache.
     *
     * @param  string $key
     *
     * @return bool
     */
    public function forget($key)
    {
        $this->rethinkdb->query()->delete(['key' => $key]);
    
        return true;
    }
    
    /**
     * Remove all items from the cache.
     *
     * @return void
     */
    public function flush()
    {
        $this->rethinkdb->query()->delete();
    }
    
    /**
     * Retrieve multiple items from the cache by key.
     *
     * Items not found in the cache will have a null value.
     *
     * @param  array $keys
     *
     * @return array
     */
    public function many(array $keys)
    {
        throw new Exception('Not implemented');
    }
    
    /**
     * Store multiple items in the cache for a given number of minutes.
     *
     * @param  array $values
     * @param  float|int $minutes
     *
     * @return void
     */
    public function putMany(array $values, $minutes)
    {
        throw new Exception('Not implemented');
    }
    
    /**
     * Get the cache key prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
    
    /**
     * Set the cache key prefix.
     *
     * @param  string  $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = ! empty($prefix) ? $prefix.':' : '';
    }
    
    /**
     * Get the current system time.
     *
     * @return int
     */
    protected function getTime()
    {
        return Carbon::now()->getTimestamp();
    }
}
