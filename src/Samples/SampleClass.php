<?php

namespace Hrpdevtools\TestingTools\Samples;

class SampleClass
{
    static public string $attrib2 = '2';
    public int $attrib1 = 1;
    public float $attrib3 = 3.1;
    public $attrib4;
    protected bool $attrib5;
    private string $attrib6;
    private array $attrib7;
    private object $attrib8;

    /**
     * @param $attrib5
     * @param $attrib6
     * @param $attrib7
     * @param $attrib8
     */
    public function __construct($attrib5, $attrib6, array $attrib7, object $attrib8)
    {
        $this->attrib5 = $attrib5;
        $this->attrib6 = $attrib6;
        $this->attrib7 = $attrib7;
        $this->attrib8 = $attrib8;
    }

    /**
     * @return bool
     */
    public function getAttrib5(): bool
    {
        return $this->attrib5;
    }

    /**
     * @return string
     */
    public function getAttrib6(): string
    {
        return $this->attrib6;
    }

    /**
     * @return array
     */
    public function getAttrib7(): array
    {
        return $this->attrib7;
    }

    /**
     * @return object
     */
    public function getAttrib8(): object
    {
        return $this->attrib8;
    }
}