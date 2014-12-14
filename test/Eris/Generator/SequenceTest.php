<?php
namespace Eris\Generator;

class SequenceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->singleElementGenerator = new Natural(0, 100);
    }

    public function testConstructWithSize()
    {
        $initialSize = 10;
        $generator = new Sequence($this->singleElementGenerator, $initialSize);
        $elements = $generator();
        $this->assertEquals($initialSize, count($elements));
    }

    public function testConstructWithSizeGenerator()
    {
        $sizeGenerator = new Natural(1, 10);
        $generator = new Sequence($this->singleElementGenerator, $sizeGenerator);
        $elements = $generator();
        $this->assertTrue($sizeGenerator->contains(count($elements)));
    }

    public function testShrink()
    {
        $initialSize = 10;
        $generator = new Sequence($this->singleElementGenerator, $initialSize);
        $elements = $generator();
        $elementsAfterShrink = $generator->shrink($elements);

        $this->assertLessThanOrEqual(count($elements), count($elementsAfterShrink));
        $this->assertLessThan($this->sumOf($elements), $this->sumOf($elementsAfterShrink));
    }

    public function testShrinkEmptySequence()
    {
        $initialSize = 0;
        $generator = new Sequence($this->singleElementGenerator, $initialSize);
        $elements = $generator();
        $this->assertEquals(0, count($elements));
        $this->assertEquals(0, count($generator->shrink($elements)));
    }

    public function testShrinkEventuallyEndsUpWithAnEmptySequence()
    {
        $initialSize = 10;
        $generator = new Sequence($this->singleElementGenerator, $initialSize);
        $elements = $generator();
        while (count($elements) > 0) {
            $elements = $generator->shrink($elements);
        }
    }

    public function testContainsElementsWhenElementsAreContainedInGivenGenerator()
    {
        $generator = new Sequence($this->singleElementGenerator, 2);
        $elements = [
            $this->singleElementGenerator->__invoke(),
            $this->singleElementGenerator->__invoke(),
        ];
        $this->assertTrue($generator->contains($elements));
    }

    public function testDoNotContainsElementsWhenElementAreNotContainedInGivenGenerator()
    {
        $aString = 'a string';
        $this->assertFalse($this->singleElementGenerator->contains($aString));
        $generator = new Sequence($this->singleElementGenerator, 2);
        $elements = [$aString, $aString];
        $this->assertFalse($generator->contains($elements));
    }

    public function testContainsAnEmptySequence()
    {
        $generator = new Sequence($this->singleElementGenerator, 2);
        $this->assertTrue($generator->contains([]));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCannotShrinkSomethingThatIsNotContainedInDomain()
    {
        $aString = 'a string';
        $this->assertFalse($this->singleElementGenerator->contains($aString));
        $generator = new Sequence($this->singleElementGenerator, 2);
        $generator->shrink([$aString]);
    }


    private function sumOf($elements)
    {
        return array_reduce(
            $elements,
            function($sum, $number) {
                return $sum + $number;
            },
            0
        );
    }
}
