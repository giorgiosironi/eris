<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class ElementsGeneratorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var int
     */
    private $size;
    /**
     * @var RandomRange
     */
    private $rand;

    protected function setUp(): void
    {
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }

    public function testGeneratesOnlyArgumentsInsideTheGivenArray()
    {
        $array = [1, 4, 5, 9];
        $generator = ElementsGenerator::fromArray($array);
        $generated = $generator($this->size, $this->rand);
        for ($i = 0; $i < 1000; $i++) {
            $this->assertContains(
                $generated->unbox(),
                $array
            );
        }
    }

    public function testASingleValueCannotShrinkGivenThereIsNoExplicitRelationshipBetweenTheValuesInTheDomain()
    {
        $generator = ElementsGenerator::fromArray(['A', 2, false]);
        $singleValue = GeneratedValueSingle::fromJustValue(2, 'elements');
        $this->assertEquals($singleValue, $generator->shrink($singleValue));
    }
}
