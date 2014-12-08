<?php
namespace Eris\Generator;
use Eris\Generator;

class OneOf implements Generator
{
    private $domain;
    
    public static function fromArray(array $domain)
    {
        return new self($domain);
    }
    
    private function __construct($domain)
    {
        $this->domain = $domain;
    }

    public function __invoke()
    {
        $index = rand(0, count($this->domain) - 1);
        return $this->domain[$index];
    }

    public function shrink($element)
    {
        return $element;
    }

    public function contains($element)
    {
        return in_array($element, $this->domain);
    }
}
