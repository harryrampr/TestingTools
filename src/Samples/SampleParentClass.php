<?php

namespace Hrpdevtools\TestingTools\Samples;

abstract class SampleParentClass
{
    public int $TestId = 0;

    public function increaseId()
    {
        return $this->TestId++;
    }

    abstract public function resetID();
}