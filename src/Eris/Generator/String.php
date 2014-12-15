<?php
namespace Eris\Generator;
use Eris\Generator;
use DomainException;

function string($maximumLength)
{
    return new String($maximumLength);
}

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
        if (!$this->contains($element)) {
            throw new DomainException(
                "Cannot shrink {$element} because does not belongs to the domain of the " .
                "Strings between 0 and {$this->maximumLength} characters"
            );
        }

        return substr($element, 0, -1);
    }

    public function contains($element)
    {
        return is_string($element) && strlen($element) <= $this->maximumLength;
    }
}
