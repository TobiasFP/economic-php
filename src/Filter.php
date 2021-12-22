<?php

namespace Economics;

use Exception;

class Filter
{
    public string $filterType = "";
    private string $operator = "";
    private array $allowedOperators = ['$eq:', '$ne:', '$gt:', '$gte:', '$lt:', '$lte:', '$like:', '$and:', '$or:', '$in:', '$nin:'];
    private string $value = "";

    public function __construct(string $filterType, string $operator, string $value)
    {
        if (!in_array($operator, $this->allowedOperators)) {
            throw new Exception("Operator not allowed.");
        }


        $this->filterType = $filterType;
        $this->operator = $operator;
        $this->value = $value;

    }

    public function toFilterString(): string
    {
        return $this->filterType . $this->operator . $this->value;
    }
}
