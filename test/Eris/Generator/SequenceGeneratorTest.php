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
            $sequenceSize = count($generator($size));

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

        $this->assertLessThanOrEqual(count($elements), count($elementsAfterShrink));
        $this->assertLessThanOrEqual(array_sum($elements), array_sum($elementsAfterShrink));
    }

    public function testShrinkEmptySequence()
    {
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $elements = $generator($size = 0);
        $this->assertEquals(0, count($elements));
        $this->assertEquals(0, count($generator->shrink($elements)));
    }

    public function testShrinkEventuallyEndsUpWithAnEmptySequence()
    {
        $numberOfShrinks = 0;
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $elements = $generator($this->size);
        while (count($elements) > 0) {
            if ($numberOfShrinks++ > 10000) {
                $this->fail('Too many shrinks');
            }
            $elements = $generator->shrink($elements);
        }
    }

    public function testContainsElementsWhenElementsAreContainedInGivenGenerator()
    {
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $elements = [
            $this->singleElementGenerator->__invoke($this->size),
            $this->singleElementGenerator->__invoke($this->size),
        ];
        $this->assertTrue($generator->contains($elements));
    }

    public function testDoNotContainsElementsWhenElementAreNotContainedInGivenGenerator()
    {
        $aString = 'a string';
        $this->assertFalse($this->singleElementGenerator->contains($aString));
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $elements = [$aString, $aString];
        $this->assertFalse($generator->contains($elements));
    }

    public function testContainsAnEmptySequence()
    {
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $this->assertTrue($generator->contains([]));
    }

    /**
     * @expectedException DomainException
     */
    public function testCannotShrinkSomethingThatIsNotContainedInDomain()
    {
        $aString = 'a string';
        $this->assertFalse($this->singleElementGenerator->contains($aString));
        $generator = new SequenceGenerator($this->singleElementGenerator);
        $generator->shrink([$aString]);
    }
}
