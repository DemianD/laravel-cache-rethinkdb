<?php

namespace Demian\RethinkDB\Tests;

use Illuminate\Support\Facades\App;
use PHPUnit\Framework\TestCase;

abstract class Test extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        App::clearResolvedInstances();
    }
    public function tearDown()
    {
        \Mockery::close();
    }
}
