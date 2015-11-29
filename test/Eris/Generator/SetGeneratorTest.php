<?php
namespace Eris\Generator;

class SetGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 100;
        $this->singleElementGenerator = new ChooseGenerator(10, 100);
    }

    public function testRespectsGenerationSize()
    {
        $generator = new SetGenerator($this->singleElementGenerator);
        $countLessThanSize = 0;
        $countEqualToSize = 0;
        for ($size = 0; $size < 400; $size++) {
            $subsetSize = count($generator($size));

            if ($subsetSize < $size) {
                $countLessThanSize++;
            }
            if ($subsetSize === $size) {
                $countEqualToSize++;
            }
        }

        $this->assertTrue(
            $countLessThanSize > 0,
            "Set generator does not generate subsets less than the size."
        );
        $this->assertTrue(
            ($countLessThanSize + $countEqualToSize) === 400,
            "Set generator has generated subsets greater than the size."
        );
    }

    public function testNoRepeatedElementsAreInTheSet()
    {
        $generator = new SetGenerator($this->singleElementGenerator);
        for ($size = 0; $size < 10; $size++) {
            $generated = $generator($size);
            $this->assertNoRepeatedElements($generated);
        }
    }

    public function testStopsBeforeInfiniteLoopsInTryingToExtractNewElementsToPutInTheSt()
    {
        $generator = new SetGenerator(new ConstantGenerator(42));
        for ($size = 0; $size < 5; $size++) {
            $generated = $generator($size);
            $this->assertLessThanOrEqual(1, count($generated));
        }
    }

    public function testShrinksOnlyInSizeBecauseShrinkingElementsMayCauseCollisions()
    {
        $generator = new SetGenerator($this->singleElementGenerator);
        $elements = $generator($this->size);
        $elementsAfterShrink = $generator->shrink($elements);

        $this->assertLessThanOrEqual(count($elements), count($elementsAfterShrink));
        $this->assertNoRepeatedElements($elementsAfterShrink);
    }

    public function testShrinkEmptySet()
    {
        $generator = new SetGenerator($this->singleElementGenerator);
        $elements = $generator($size = 0);
        $this->assertEquals(0, count($elements));
        $this->assertEquals(0, count($generator->shrink($elements)));
    }

    public function testContainsElementsWhenElementsAreContainedInGivenGenerator()
    {
        $generator = new SetGenerator($this->singleElementGenerator);
        $elements = [
            $this->singleElementGenerator->__invoke($this->size),
            $this->singleElementGenerator->__invoke($this->size),
        ];
        $this->assertTrue($generator->contains($elements));
    }

    public function testDoesNotContainElementsWhenElementsAreNotContainedInGivenGenerator()
    {
        $aString = 'a string';
        $this->assertFalse($this->singleElementGenerator->contains($aString));
        $generator = new SetGenerator($this->singleElementGenerator);
        $elements = [$aString, $aString];
        $this->assertFalse($generator->contains($elements));
    }

    public function testContainsAnEmptySet()
    {
        $generator = new SetGenerator($this->singleElementGenerator);
        $this->assertTrue($generator->contains([]));
    }

    private function assertNoRepeatedElements($generated)
    {
        sort($generated);
        $this->assertTrue(
            array_unique($generated) === $generated,
            "There are repeated elements inside a generated value: " 
            . var_export($generated, true)
        );
    }

}
