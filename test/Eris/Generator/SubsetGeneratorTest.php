<?php
namespace Eris\Generator;

use Eris\Quantifier\ForAll;

class SubsetGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testScalesGenerationSizeToTouchAllPossibleSubsets()
    {
        $universe = ['a', 'b', 'c', 'd', 'e'];
        $generator = new SubsetGenerator($universe);
        $maxSize = ForAll::DEFAULT_MAX_SIZE;
        $subsetSizes = [];
        for ($size = 0; $size < $maxSize; $size++) {
            $subsetSizes[] = count($generator($size));
        }

        $subsetSizeFrequencies = array_count_values($subsetSizes);
        var_dump($subsetSizeFrequencies);
        // notice the full universe is very rarely generated
        // hence its presence is not asserted here
        for ($subsetSize = 0; $subsetSize < count($universe); $subsetSize++) {
            $this->assertGreaterThan(
                0,
                $subsetSizeFrequencies[$subsetSize],
                "There were no subsets generated of size $subsetSize"
            );
        }

    }
}
