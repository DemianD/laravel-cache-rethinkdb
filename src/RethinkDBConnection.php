<?php namespace Demian\RethinkDB;

use Illuminate\Database\Connection;
use r;

class RethinkDBConnection extends Connection
{
    /**
     * @var \r\Connection
     */
    protected $connection;
    
    /**
     * @var \r\Queries\Dbs\Db
     */
    protected $db;
    
    public function __construct($database, $prefix, $config)
    {
        $this->connection = r\connect($config['host'].':'.$config['port']);
        $this->db = r\db($database);
        
        $this->useDefaultPostProcessor();
        $this->useDefaultQueryGrammar();
    }
    
    protected function getDefaultQueryGrammar()
    {
        return new Grammar;
    }
    
    protected function getDefaultPostProcessor()
    {
        return new Processor;
    }
    
    public function getDatabase()
    {
        return $this->db;
    }
    
    public function query()
    {
        return new Query\Query($this->connection, $this->db, $this->table);
    }
}
