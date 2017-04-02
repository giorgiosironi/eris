<?php
namespace Eris\Generator;

class SetGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->size = 100;
        $this->singleElementGenerator = new ChooseGenerator(10, 100);
        $this->rand = 'rand';
    }

    public function testRespectsGenerationSize()
    {
        $generator = new SetGenerator($this->singleElementGenerator);
        $countLessThanSize = 0;
        $countEqualToSize = 0;
        for ($size = 0; $size < 400; $size++) {
            $subsetSize = count($generator($size, $this->rand)->unbox());

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
            $generated = $generator($size, $this->rand)->unbox();
            $this->assertNoRepeatedElements($generated);
        }
    }

    public function testStopsBeforeInfiniteLoopsInTryingToExtractNewElementsToPutInTheSt()
    {
        $generator = new SetGenerator(new ConstantGenerator(42));
        for ($size = 0; $size < 5; $size++) {
            $generated = $generator($size, $this->rand);
            $this->assertLessThanOrEqual(1, count($generated->unbox()));
        }
    }

    public function testShrinksOnlyInSizeBecauseShrinkingElementsMayCauseCollisions()
    {
        $generator = new SetGenerator($this->singleElementGenerator);
        $elements = $generator($this->size, $this->rand);
        $elementsAfterShrink = $generator->shrink($elements);

        $this->assertLessThanOrEqual(count($elements->unbox()), count($elementsAfterShrink->unbox()));
        $this->assertNoRepeatedElements($elementsAfterShrink->unbox());
    }

    public function testShrinkEmptySet()
    {
        $generator = new SetGenerator($this->singleElementGenerator);
        $elements = $generator($size = 0, $this->rand);
        $this->assertEquals(0, count($elements->unbox()));
        $this->assertEquals(0, count($generator->shrink($elements)->unbox()));
    }

    private function assertNoRepeatedElements(array $generated)
    {
        sort($generated);
        $this->assertTrue(
            array_unique($generated) === $generated,
            "There are repeated elements inside a generated value: "
            . var_export($generated, true)
        );
    }
}
