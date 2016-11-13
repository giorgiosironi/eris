<?php
namespace Eris\Generator;

use Eris\Generator;
use DomainException;

function seq(Generator $singleElementGenerator)
{
    // TODO: Generator::box($singleElementGenerator);
    if (!($singleElementGenerator instanceof Generator)) {
        $singleElementGenerator = new Constant($singleElementGenerator);
    }
    return new SequenceGenerator($singleElementGenerator);
}

class SequenceGenerator implements Generator
{
    private $singleElementGenerator;

    public function __construct(Generator $singleElementGenerator)
    {
        $this->singleElementGenerator = $singleElementGenerator;
    }

    public function __invoke($size, $rand)
    {
        $sequenceLength = $rand(0, $size);
        return $this->vector($sequenceLength)->__invoke($size, $rand);
    }

    public function shrink(GeneratedValue $sequence)
    {
        if (!$this->contains($sequence)) {
            throw new DomainException(
                'Cannot shrink {' . var_export($sequence, true) . '} because ' .
                'it does not belong to the domain of this sequence'
            );
        }

        // TODO: make deterministic, try first one then the other?
        $willShrinkInSize = (new BooleanGenerator())->__invoke(1, 'rand');
        if ($willShrinkInSize) {
            return $this->shrinkInSize($sequence);
        }
        if (!$willShrinkInSize) {
            return $this->shrinkTheElements($sequence);
        }
    }

    public function contains(GeneratedValue $sequence)
    {
        return $this->vector(count($sequence->unbox()))->contains($sequence);
    }

    private function shrinkInSize($sequence)
    {
        if (count($sequence->unbox()) === 0) {
            return $sequence;
        }

        $input = $sequence->input();
        $indexOfElementToRemove = array_rand($input);
        unset($input[$indexOfElementToRemove]);
        $input = array_values($input);
        return GeneratedValue::fromValueAndInput(
            array_map(
                function ($element) {
                    return $element->unbox();
                },
                $input
            ),
            $input,
            'sequence'
        );
    }

    private function shrinkTheElements($sequence)
    {
        return $this->vector(count($sequence))->shrink($sequence);
    }

    private function vector($size)
    {
        return new VectorGenerator($size, $this->singleElementGenerator);
    }
}
