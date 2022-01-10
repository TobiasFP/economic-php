<?php

namespace Economics\Objects;

class Line
{
    public string $description;
    public int $lineNumber;
    public int $quantity;
    public object $product;
    public function __construct(object $product, string $description = '', int $quantity = 1, int $lineNumber = 1)
    {
        $this->description = $description;
        $this->lineNumber = $lineNumber;
        $this->quantity = $quantity;
        $this->product = $product;
    }
}