<?php

namespace Demian\RethinkDB\Models;

class CacheItem
{
    public $id;
    public $key;
    public $value;
    public $expiration;
    
    public function __construct($id, $key, $value, $expiration)
    {
        $this->id = $id;
        $this->key = $key;
        $this->value = $value;
        $this->expiration = $expiration;
    }
    
    public function toArray()
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
            'expiration' => $this->expiration
        ];
    }
}
