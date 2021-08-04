<?php

namespace Tests\Unit\Helpers;

use App\Helpers\SessionStack;
use Tests\TestCase;

class SessionStackTest extends TestCase
{


    public function testPush()
    {
        $stack = new SessionStack('some_key');
        $stack->push(1);
        $stack->push(2);
        $this->assertEquals([1, 2], $stack->get());
    }

    public function testSet()
    {
        $stack = new SessionStack('some_key');
        $stack->set(['a', 'b']);
        $this->assertEquals(['a', 'b'], $stack->get());
    }

    public function testReset()
    {
        $stack = new SessionStack('some_key');
        $stack->push(1);
        $stack->push(2);
        $this->assertCount(2, $stack->get());
        $stack->reset();
        $this->assertCount(0, $stack->get());
    }

    public function testGet()
    {
        $stack = new SessionStack('some_key');
        $stack->set(['a', 'b', 5]);
        $this->assertEquals(['a', 'b', 5], $stack->get());
    }
}
