<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class SequenceGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private int $size;
    private \Eris\Generator\ChooseGenerator $singleElementGenerator;
    private \Eris\Random\RandomRange $rand;

    protected function setUp(): void
    {
        $this->size = 100;
        $this->singleElementGenerator = new ChooseGenerator(10, 100);
        $this->rand = new RandomRange(new RandSource());
    }

    public function testRespectsGenerationSize(): void
    {
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $countLessThanSize = 0;
        $countEqualToSize = 0;
        for ($size = 0; $size < 400; $size++) {
            $sequenceSize = count($generator($size, $this->rand)->unbox());

            if ($sequenceSize < $size) {
                $countLessThanSize++;
            }
            if ($sequenceSize === $size) {
                $countEqualToSize++;
            }
        }

        $this->assertTrue(
            $countLessThanSize > 0,
            "Sequence generator does not generate sequences less than the size."
        );
        $this->assertTrue(
            ($countLessThanSize + $countEqualToSize) === 400,
            "Sequence generator has generated sequences greater than the size."
        );
    }

    public function testShrink(): void
    {
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $elements = $generator($this->size, $this->rand);
        $elementsAfterShrink = $generator->shrink($elements);
        if ($elementsAfterShrink->count() === 0) {
            // the generated value couldn't be shrunk
            return;
        }

        $this->assertLessThanOrEqual(count($elements->unbox()), count($elementsAfterShrink->unbox()));
        $this->assertLessThanOrEqual(array_sum($elements->unbox()), array_sum($elementsAfterShrink->unbox()));
    }

    public function testShrinkEmptySequence(): void
    {
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $elements = $generator($size = 0, $this->rand);
        $this->assertEquals(0, count($elements->unbox()));
        $this->assertEquals(0, count($generator->shrink($elements)));
    }

    public function testShrinkEventuallyEndsUpWithNoOptions(): void
    {
        $numberOfShrinks = 0;
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $value = $generator($this->size, $this->rand);
        $options = $generator->shrink($value);
        while (count($options) > 0) {
            if ($numberOfShrinks++ > 100) {
                $this->fail('Too many shrinks');
            }
            $options = $generator->shrink($options->first());
        }
        $this->assertEquals(0, $options->count());
    }
}
