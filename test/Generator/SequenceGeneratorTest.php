<?php
namespace Eris\Generator;

class SequenceGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 100;
        $this->singleElementGenerator = new ChooseGenerator(10, 100);
    }

    public function testRespectsGenerationSize()
    {
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $countLessThanSize = 0;
        $countEqualToSize = 0;
        for ($size = 0; $size < 400; $size++) {
            $sequenceSize = count($generator($size)->unbox());

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

    public function testShrink()
    {
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $elements = $generator($this->size);
        $elementsAfterShrink = $generator->shrink($elements);

        $this->assertLessThanOrEqual(count($elements->unbox()), count($elementsAfterShrink->unbox()));
        $this->assertLessThanOrEqual(array_sum($elements->unbox()), array_sum($elementsAfterShrink->unbox()));
    }

    public function testShrinkEmptySequence()
    {
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $elements = $generator($size = 0);
        $this->assertEquals(0, count($elements->unbox()));
        $this->assertEquals(0, count($generator->shrink($elements)->unbox()));
    }

    public function testShrinkEventuallyEndsUpWithAnEmptySequence()
    {
        $numberOfShrinks = 0;
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $elements = $generator($this->size);
        while (count($elements->unbox()) > 0) {
            if ($numberOfShrinks++ > 10000) {
                $this->fail('Too many shrinks');
            }
            $elements = $generator->shrink($elements);
        }
    }

    public function testContainsAnEmptySequence()
    {
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $this->assertTrue($generator->contains(GeneratedValue::fromJustValue([])));
    }
}
