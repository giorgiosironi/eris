<?php
namespace Eris\Generator;
use Eris\Generator;

class String implements Generator
{
    private $maximumLength;
    
    public function __construct($maximumLength)
    {
        $this->maximumLength = $maximumLength;
    }

    public function __invoke()
    {
        $length = rand(0, $this->maximumLength);
        $built = '';
        for ($i = 0; $i < $length; $i++) {
            $built .= chr(rand(33, 127));
        }
        return $built;
    }

    public function shrink($element)
    {
    }

    public function contains($element)
    {
    }
}
