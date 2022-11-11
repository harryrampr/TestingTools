<?php

namespace TestingTools\Samples;

final class SampleFinalClass
{

    private int $initVal = 1;

    public function __construct()
    {
        $this->initVal++;
    }

    public function resetIniVal(): int
    {
        return $this->initVal;
    }
}