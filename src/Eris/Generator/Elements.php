<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

function elements(/*$a, $b, ...*/)
{
    $arguments = func_get_args();
    if (count($arguments) == 1) {
        return Generator\Elements::fromArray($arguments[0]);
    } else {
        return Generator\Elements::fromArray($arguments);
    }
}


class Elements implements Generator
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

    public function __invoke($_size)
    {
        $index = rand(0, count($this->domain) - 1);
        return $this->domain[$index];
    }

    public function shrink($element)
    {
        if (!$this->contains($element)) {
            throw new DomainException(
                $element . ' does not belong to the domain with elements ' .
                var_export($this->domain, true)
            );
        }

        return $element;
    }

    public function contains($element)
    {
        return in_array($element, $this->domain);
    }
}
