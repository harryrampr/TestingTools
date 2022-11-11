<?php

namespace Hrpdevtools\TestingTools\Samples;

trait SampleTrait
{
    protected int $test = 10;

    /**
     * @param int $test
     */
    public function setTest(int $test): void
    {
        $this->test = $test;
    }
    
    /**
     * @return int
     */
    public function getTest(): int
    {
        return $this->test;
    }

}