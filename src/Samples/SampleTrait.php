<?php

namespace TestingTools\Samples;

trait SampleTrait
{
    protected int $test = 10;

    /**
     * @return int
     */
    public function getTest(): int
    {
        return $this->test;
    }

    /**
     * @param int $test
     */
    public function setTest(int $test): void
    {
        $this->test = $test;
    }

}