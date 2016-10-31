<?php namespace Demian\RethinkDB;

use Illuminate\Database\Connection;
use r;

class RethinkDBConnection
{
    /**
     * @var \r\Connection
     */
    protected $connection;
    
    /**
     * @var \r\Queries\Dbs\Db
     */
    protected $db;
    
    /**
     * @var
     */
   protected $table;
    
    /**
     * RethinkDBConnection constructor.
     *
     * @param $host
     * @param $port
     * @param $database
     * @param $table
     */
    public function __construct($host, $port, $database, $table)
    {
        $this->connection = r\connect($host.':'.$port);
        $this->db = r\db($database);
        
        $this->table = $table;
    }
    
    public function getDatabase()
    {
        return $this->db;
    }
    
    public function getConnection()
    {
        return $this->connection;
    }
    
    public function query()
    {
        return new Query\Query($this->db, $this->connection, $this->table);
    }
}
