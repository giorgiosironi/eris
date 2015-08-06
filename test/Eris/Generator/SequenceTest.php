<?php
namespace Eris\Generator;

class SequenceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 100;
        $this->singleElementGenerator = new Choose(10, 100);
    }

    public function testConstructWithSize()
    {
        $generator = new Sequence($this->singleElementGenerator);
        $elements = $generator($this->size);
        $this->assertEquals($this->size, count($elements));
    }

    public function testShrink()
    {
        $generator = new Sequence($this->singleElementGenerator);
        $elements = $generator($this->size);
        $elementsAfterShrink = $generator->shrink($elements);

        $this->assertLessThanOrEqual(count($elements), count($elementsAfterShrink));
        $this->assertLessThan(array_sum($elements), array_sum($elementsAfterShrink));
    }

    public function testShrinkEmptySequence()
    {
        $generator = new Sequence($this->singleElementGenerator);
        $elements = $generator($size = 0);
        $this->assertEquals(0, count($elements));
        $this->assertEquals(0, count($generator->shrink($elements)));
    }

    public function testShrinkEventuallyEndsUpWithAnEmptySequence()
    {
        $numberOfShrinks = 0;
        $generator = new Sequence($this->singleElementGenerator);
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
        $generator = new Sequence($this->singleElementGenerator);
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
        $generator = new Sequence($this->singleElementGenerator);
        $elements = [$aString, $aString];
        $this->assertFalse($generator->contains($elements));
    }

    public function testContainsAnEmptySequence()
    {
        $generator = new Sequence($this->singleElementGenerator);
        $this->assertTrue($generator->contains([]));
    }

    /**
     * @expectedException DomainException
     */
    public function testCannotShrinkSomethingThatIsNotContainedInDomain()
    {
        $aString = 'a string';
        $this->assertFalse($this->singleElementGenerator->contains($aString));
        $generator = new Sequence($this->singleElementGenerator);
        $generator->shrink([$aString]);
    }
}
