<?php

use PHPUnit\Framework\TestCase;
use Economics\Filters;
use Economics\Filter;

class filtersTest extends TestCase
{

    public function testFilters()
    {
        $filter = new Filter('name', '$eq:', 'Tobias');
        $filters = new Filters([$filter]);
        self::assertEquals('filter=name$eq:Tobias', $filters->filter());
    }

    public function testMultipleFilters()
    {
        $filter = new Filter('name', '$eq:', 'Tobias');
        $filterTwo = new Filter('date', '$eq:', '2021-12-02');
        $filters = new Filters([$filter, $filterTwo]);
        self::assertEquals('filter=name$eq:Tobias$and:date$eq:2021-12-02', $filters->filter());
    }

}