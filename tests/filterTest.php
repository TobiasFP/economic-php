<?php

use PHPUnit\Framework\TestCase;
use Tobiasfp\Economics\Filter;

class filterTest extends TestCase
{
    private Filter $filter;

    public function setup(): void
    {
        $this->filter = new Filter('name', '$eq:', 'Tobias');
    }

    public function testFilterToString()
    {
        self::assertEquals('name$eq:Tobias', $this->filter->toFilterString());
    }

}