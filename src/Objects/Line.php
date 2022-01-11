<?php

namespace Economics\Objects;

class Line
{
    public string $description;
    public int $lineNumber;
    public int $quantity;
    public object $product;
    public array $unit;
    public float $unitNetPrice;
    public float $totalNetAmount;

    public function __construct(object $product, Unit $unit, string $description = '', int $quantity = 1, float $unitNetPrice = 0, float $totalNetAmount = 0, int $lineNumber = 1)
    {
        $this->description = $description;
        $this->lineNumber = $lineNumber;
        $this->quantity = $quantity;
        $this->product = $product;
        $this->unit = (array)$unit;
        $this->unitNetPrice = $unitNetPrice;
        $this->totalNetAmount = $totalNetAmount;
    }
}