<?php namespace Demian\RethinkDB\tests;

use Carbon\Carbon;
use Demian\RethinkDB\Models\CacheItem;
use Demian\RethinkDB\Query\Query;
use Demian\RethinkDB\RethinkDBConnection;
use Demian\RethinkDB\RethinkDBStore;
use Demian\RethinkDB\Tests\Test;
use Illuminate\Support\Facades\DB;
use Mockery;

class StoreTest extends Test
{
    
    /**
     * @var RethinkDBStore
     */
    private $store;
    
    /**
     * @var Query
     */
    private $query;
    
    public function setUp()
    {
        parent::setUp();
    
        Carbon::setTestNow(Carbon::create('2016', '10', '31', '00', '00', '00', 'utc'));
        
        $connection = Mockery::mock(RethinkDBConnection::class);
        $query = Mockery::mock(Query::class);
        
        $connection->shouldReceive('query')->andReturn($query);
        
        $this->query = $query;
        $this->store = new RethinkDBStore($connection);
    }
    
    public function test_get_key_not_exist()
    {
        $this->query->shouldReceive('first')->with(['key' => 'key_1'])->andReturn(null);
        
        $res = $this->store->get('key_1');
        $this->assertEquals(null, $res);
    }
    
    public function test_get_key_exist()
    {
        $item = new CacheItem('id', 'key_1', 'value', Carbon::tomorrow()->getTimestamp());
        $this->query->shouldReceive('first')->with(['key' => 'key_1'])->andReturn($item);
    
        $res = $this->store->get('key_1');
        $this->assertEquals('value', $res);
    }
    
    public function test_get_key_exist_expired()
    {
        $item = new CacheItem('id', 'key_1', 'value', Carbon::yesterday()->getTimestamp());
        
        $this->query->shouldReceive('first')->with(['key' => 'key_1'])->andReturn($item);
        $this->query->shouldReceive('delete')->with(['key' => 'key_1']);
        
        $res = $this->store->get('key_1');
        $this->assertEquals(null, $res);
    }
    
    public function test_put_key_not_exist()
    {
        $this->query->shouldReceive('first')->with(['key' => 'key_1'])->andReturn(null);
        $this->query->shouldReceive('create')->with(['key' => 'key_1', 'value' => 'value', 'expiration' => 1477875600]);
        
        $res = $this->store->put('key_1', 'value');
    }
    
    public function test_put_key_exist()
    {
        $item = new CacheItem('id', 'key_1', 'value', Carbon::yesterday()->getTimestamp());
        
        $this->query->shouldReceive('first')->with(['key' => 'key_1'])->andReturn($item);
        
        $this->query->shouldReceive('update')->with($item);
        
        $res = $this->store->put('key_1', 'value');
    }
    
    public function test_increment()
    {
        $this->query->shouldReceive('incrementOrDecrement')->with('key_1', '1')->once();
        $this->query->shouldReceive('incrementOrDecrement')->with('key_1', '2')->once();
        
        $res = $this->store->increment('key_1');
        $res = $this->store->increment('key_1', 2);
    }
    
    public function test_decrement()
    {
        $this->query->shouldReceive('incrementOrDecrement')->with('key_1', '-1')->once();
        $this->query->shouldReceive('incrementOrDecrement')->with('key_1', '-2')->once();
        
        $res = $this->store->decrement('key_1');
        $res = $this->store->decrement('key_1', 2);
    }
}
