<?php

namespace Tobiasfp\Economics;

use Exception;

class Filters
{
    private array $filterable;

    private array $operator;

    private array $value;

    private string $filterString = "";

    public function __construct(array $filterable = [], array $operator = [], array $value = [])
    {
        $this->filterable = $filterable;
        $this->operator = $operator;
        $this->value = $value;
    }

    public function filter(): string
    {
        foreach ($this->filterable as $key => $value) {
            if ($this->filterString === "") {
                $this->filterString = 'filter=' . $this->filterable[$key] . $this->operator[$key] . $this->value[$key];
            } else {
                $this->filterString .= '$and:' . $this->filterable[$key] . $this->operator[$key] . $this->value[$key];
            }
        }

        return $this->filterString;
    }
}

class Filter
{
    private string $filterType = "";
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
