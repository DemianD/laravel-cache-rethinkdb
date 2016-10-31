<?php namespace Demian\RethinkDB\Query;

use Demian\RethinkDB\Models\CacheItem;
use Demian\RethinkDB\RethinkDBConnection;
use r\Queries\Tables\Table;

class Query
{
    private $db;
    private $connection;
    private $table;
    
    public function __construct($db, $connection, $table)
    {
        $this->db = $db;
        $this->connection = $connection;
        $this->table = $table;
    }
    
    public function first($filter = null)
    {
        $query = $this->table();
        
        if (!is_null($filter)) {
            $query = $query->filter($filter)->limit(1);
        }
        
        $result = $this->execute($query)->toArray();
        
        if (empty($result)) {
            return null;
        }
        
        $item = (array)array_first($result);
        
        return new CacheItem($item['id'], $item['key'], $item['value'], $item['expiration']);
    }
    
    public function create($document)
    {
        $query = $this->table()->insert($document);
        
        $this->execute($query);
    }
    
    public function update(CacheItem $item)
    {
        $query = $this->table()->get($item->id)->update($item->toArray());
        
        $this->execute($query);
    }
    
    public function delete($key = null)
    {
        if (is_null($key)) {
            $query = $this->table()->delete();
        } else {
            $query = $this->table()->filter(['key' => $key])->delete();
        }
        
        $this->execute($query);
    }
    
    public function incrementOrDecrement($key, $value)
    {
        $query = $this->table()->filter(['key' => $key])->update(['value' => \r\row('value')->coerceTo("Number")->add($value)]);
        
        $this->execute($query);
    }
    /**
     * @return Table
     */
    public function table()
    {
        return $this->db->table($this->table);
    }
    
    public function execute($query)
    {
        return $query->run($this->connection);
    }
}
