<?php
namespace Eris\Generator;

use Eris\Quantifier\ForAll;

class SubsetGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->universe = ['a', 'b', 'c', 'd', 'e'];
        $this->generator = new SubsetGenerator($this->universe);
        $this->size = 100;
        $this->rand = 'rand';
    }
    
    public function testScalesGenerationSizeToTouchAllPossibleSubsets()
    {
        $maxSize = ForAll::DEFAULT_MAX_SIZE;
        $subsetSizes = [];
        for ($size = 0; $size < $maxSize; $size++) {
            $subsetSizes[] = count($this->generator->__invoke($size, $this->rand)->unbox());
        }

        $subsetSizeFrequencies = array_count_values($subsetSizes);
        // notice the full universe is very rarely generated
        // hence its presence is not asserted here
        for ($subsetSize = 0; $subsetSize < count($this->universe); $subsetSize++) {
            $this->assertGreaterThan(
                0,
                $subsetSizeFrequencies[$subsetSize],
                "There were no subsets generated of size $subsetSize"
            );
        }
    }

    public function testNoRepeatedElementsAreInTheSet()
    {
        for ($size = 0; $size < ForAll::DEFAULT_MAX_SIZE; $size++) {
            $generated = $this->generator->__invoke($size, $this->rand)->unbox();
            $this->assertNoRepeatedElements($generated);
        }
    }

    public function testShrinksOnlyInSizeBecauseShrinkingElementsMayCauseCollisions()
    {
        $elements = $this->generator->__invoke($this->size, $this->rand);
        $elementsAfterShrink = $this->generator->shrink($elements);

        $this->assertLessThanOrEqual(
            count($elements->unbox()),
            count($elementsAfterShrink->unbox())
        );
        $this->assertNoRepeatedElements($elementsAfterShrink->unbox());
    }

    public function testShrinkEmptySet()
    {
        $elements = $this->generator->__invoke($size = 0, $this->rand);
        $this->assertEquals(0, count($elements->unbox()));
        $this->assertEquals(0, count($this->generator->shrink($elements)->unbox()));
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
