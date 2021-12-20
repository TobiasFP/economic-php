<?php

namespace Economics;

class Filters
{
    private array $filters;
    private string $filterString = "";

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function filter(): string
    {
        foreach ($this->filters as $filter) {
            if ($this->filterString === "") {
                $this->filterString = 'filter=' . $filter->toFilterString();
            } else {
                $this->filterString .= '$and:' . $filter->toFilterString();
            }
        }
        return $this->filterString;
    }
}