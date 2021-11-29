<?php

use Tobiasfp\Economics\Economics;
use PHPUnit\Framework\TestCase;


class economicsTest extends TestCase
{
    private Economics $econ;

    public function setup(): void {
        $this->econ = new Economics("demo", "demo");
    }

    public function testCustomers()
    {
        self::assertEquals($this->econ->customers()[0]->customerNumber, 1);
    }
    
    public function testGetCustomer() {
        self::assertEquals($this->econ->customer(1)->customerNumber, 1);
    }
}
