<?php
namespace Eris\Generator;

use Eris\Generator\GeneratedValue;
use InvalidArgumentException;

final class GeneratedValue
{
    private $value;
    private $input;
    private $generatorName;
    
    public static function fromValueAndInput($value, $input, $generatorName = null)
    {
        return new self($value, $input, $generatorName);
    }

    public static function fromJustValue($value, $generatorName = null)
    {
        return new self($value, $value, $generatorName);
    }
    
    private function __construct($value, $input, $generatorName, array $annotations = [])
    {
        if ($value instanceof self) {
            throw new InvalidArgumentException("It looks like you are trying to build a GeneratedValue whose value is another GeneratedValue. This is almost always an error as values will be passed as-is to properties and GeneratedValue should be hidden from them.");
        }
        $this->value = $value;
        $this->input = $input;
        $this->generatorName = $generatorName;
        $this->annotations = $annotations;
    }

    public function input()
    {
        return $this->input;
    }

    public function unbox()
    {
        return $this->value;
    }

    public function __toString()
    {
        return var_export($this, true);
    }

    public function map(callable $applyToValue, $generatorName)
    {
        return new self(
            $applyToValue($this->value),
            $this,
            $generatorName
        );
    }

    public function derivedIn($generatorName)
    {
        return $this->map(
            function($value) { return $value; },
            $generatorName
        );
    }

    public function annotate($key, $value)
    {
        $annotations = $this->annotations;
        $annotations[$key] = $value;
        return new self(
            $this->value,
            $this->input,
            $this->generatorName,
            $annotations
        );
    }

    /**
     * TODO: docblock, validation
     */
    public function annotation($key)
    {
        return $this->annotations[$key];
    }
}
