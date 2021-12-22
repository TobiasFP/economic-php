<?php

namespace Economics;

use Economics\Exceptions\InvalidFilterException;

class Filters
{
    private array $filters;
    private string $filterString = "";
    private array $allowedCustomerFilters = ['address', 'balance', 'barred', 'city', 'corporateIdentificationNumber', 'country', 'creditLimit', 'currency', 'customerNumber', 'ean', 'email', 'lastUpdated', 'mobilePhone', 'name', 'publicEntryNumber', 'telephoneAndFaxNumber', 'vatNumber', 'website', 'zip'];

    /**
     * @throws InvalidFilterException
     */
    public function __construct(array $filters = [], string $type = '')
    {
        foreach ($filters as $filter) {
            if (gettype($filter) !== 'object' || get_class($filter) !== 'Economics\Filter') {
                throw new InvalidFilterException("Filter not valid");
            }

            if ($type === 'customer') {
                if (!in_array($filter->filterType, $this->allowedCustomerFilters)) {
                    throw new InvalidFilterException("Filtertype not allowed: " . $filter->filterType);
                }
            }
        }
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