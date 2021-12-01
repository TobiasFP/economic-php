<?php

use PHPUnit\Framework\TestCase;
use Tobiasfp\Economics\Filter;

class filterTest extends TestCase
{
    private Economics $econ;

    public function setup(): void
    {
        $this->econ = new Economics("demo", "demo");
    }

    public function testFilterToString()
    {
        //self::assertEquals(1, $this->econ->customers()[0]->customerNumber);
    }

}