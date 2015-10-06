<?php
namespace Eris\Generator;
use DomainException;

class ContainmentCheck
{
    private $generator;
    
    public static function of($generator)
    {
        return new self($generator);
    }
    
    private function __construct($generator)
    {
        $this->generator = $generator;
    }
    
    public function on($value)
    {
        if (!$this->generator->contains($value)) {
            throw new DomainException(
                'Cannot shrink {' . var_export($value, true) . '} because ' .
                'it does not belong to the domain of this Generator'
            );
        }
    }
}
