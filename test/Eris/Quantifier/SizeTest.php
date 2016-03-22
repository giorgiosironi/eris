<?php
namespace Eris\Quantifier;

class SizeTest extends \PHPUnit_Framework_TestCase
{
    public function testProducesAListOfSizesIncreasingThemTriangularly()
    {
        $size = Size::withTriangleGrowth(1000);
        $this->assertEquals(0, $size->at(0));
        $this->assertEquals(1, $size->at(1));
        $this->assertEquals(3, $size->at(2));
        $this->assertEquals(6, $size->at(3));
        $this->assertEquals(10, $size->at(4));
    }

    public function testCyclesThroughAvailableSizesWhenTheyAreFinished()
    {
        $size = Size::withTriangleGrowth(1000);
        $this->assertInternalType('integer', $size->at(42000));
    }

    public function testAllowsLinearGrowth()
    {
        $size = Size::withLinearGrowth(1000);
        $this->assertEquals(0, $size->at(0));
        $this->assertEquals(1, $size->at(1));
        $this->assertEquals(2, $size->at(2));
        $this->assertEquals(3, $size->at(3));
        $this->assertEquals(4, $size->at(4));
    }

    public static function limits()
    {
        return [
            [2],
            [5],
            [10],
            [20],
            [100],
            [10000],
        ];
    }

    /**
     * @dataProvider limits
     */
    public function testCoversAUniformSubsetWhenLimitedToTheNumberOfIterations($limit)
    {
        $size = Size::withTriangleGrowth(1000)
            ->limit($limit);
        $this->assertEquals($limit, count($size));
        $this->assertEquals(0, $size->at(0));
        $this->assertEquals(990, $size->at($limit - 1));
        $this->assertEquals(0, $size->at($limit));
    }
}
