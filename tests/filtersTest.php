<?php

use Economics\Exceptions\InvalidFilterException;
use Economics\Filter;
use Economics\Filters;
use PHPUnit\Framework\TestCase;

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

    public function testBadFilter()
    {
        $this->expectException(InvalidFilterException::class);
        new Filters(["test"]);
    }

    public function testDisallowedFiltertype()
    {
        $this->expectException(InvalidFilterException::class);
        $filter = new Filter('badFilterType', '$eq:', 'Tobias');
        $filters = new Filters([$filter], 'customer');

    }
}