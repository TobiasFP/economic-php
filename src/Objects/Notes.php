<?php

namespace Economics\Objects;

class Notes
{

    public string $heading;
    public string $textLine1;
    public string $textLine2;

    public function __construct(string $heading = '', string $textLine1 = '', string $textLine2 = '')
    {
        $this->heading = $heading;
        $this->textLine1 = $textLine1;
        $this->textLine2 = $textLine2;
    }

    public function isValid(): bool
    {
        if ($this->heading !== '' || $this->textLine1 !== '' || $this->textLine2 !== '') {
            return true;
        }
        return false;
    }
}