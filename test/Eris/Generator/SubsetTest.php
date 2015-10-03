<?php
namespace Eris\Generator;

class SubsetTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // TODO: actually useful?
        $this->size = 100;

        $this->singleElementGenerator = new Choose(10, 100);
    }

    public function testRespectsGenerationSize()
    {
        $generator = new Subset($this->singleElementGenerator);
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
            "Subset generator does not generate subsets less than the size."
        );
        $this->assertTrue(
            ($countLessThanSize + $countEqualToSize) === 400,
            "Subset generator has generated subsets greater than the size."
        );
    }

    public function testNoRepeatedElementsAreInTheSet()
    {
        $generator = new Subset($this->singleElementGenerator);
        for ($size = 0; $size < 2; $size++) {
            $generated = $generator($size);
            sort($generated);
            $this->assertTrue(
                array_unique($generated) === $generated,
                "There are repeated elements inside a generated value: " 
                . var_export($generated, true)
            );
        }
    }
}
