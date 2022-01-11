<?php

namespace Economics\Objects;

class Unit
{
    public int $unitNumber;
    public int $unitCostPrice;
    public int $unitNetPrice;

    public function __construct(int $unitNumber, int $unitCostPrice = 0, int $unitNetPrice = 0)
    {
        $this->unitNumber = $unitNumber;
        $this->unitCostPrice = $unitCostPrice;
        $this->unitNetPrice = $unitNetPrice;
    }
}