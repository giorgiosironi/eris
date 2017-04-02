<?php
namespace Eris\Generator;

class SequenceGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->size = 100;
        $this->singleElementGenerator = new ChooseGenerator(10, 100);
        $this->rand = 'rand';
    }

    public function testRespectsGenerationSize()
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

    public function testShrink()
    {
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $elements = $generator($this->size, $this->rand);
        $elementsAfterShrink = $generator->shrink($elements);
        if ($elementsAfterShrink->count() == 0) {
            // the generated value couldn't be shrunk
            return;
        }

        $this->assertLessThanOrEqual(count($elements->unbox()), count($elementsAfterShrink->unbox()));
        $this->assertLessThanOrEqual(array_sum($elements->unbox()), array_sum($elementsAfterShrink->unbox()));
    }

    public function testShrinkEmptySequence()
    {
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $elements = $generator($size = 0, $this->rand);
        $this->assertEquals(0, count($elements->unbox()));
        $this->assertEquals(0, count($generator->shrink($elements)));
    }

    public function testShrinkEventuallyEndsUpWithNoOptions()
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
