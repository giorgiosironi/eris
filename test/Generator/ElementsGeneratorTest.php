<?php
namespace Eris\Generator;

use Eris\Random\RandomRange;
use Eris\Random\RandSource;

class ElementsGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private int $size;
    private \Eris\Random\RandomRange $rand;

    protected function setUp(): void
    {
        $this->size = 10;
        $this->rand = new RandomRange(new RandSource());
    }

    public function testGeneratesOnlyArgumentsInsideTheGivenArray(): void
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

    public function testASingleValueCannotShrinkGivenThereIsNoExplicitRelationshipBetweenTheValuesInTheDomain(): void
    {
        $generator = ElementsGenerator::fromArray(['A', 2, false]);
        $singleValue = GeneratedValueSingle::fromJustValue(2, 'elements');
        $this->assertEquals($singleValue, $generator->shrink($singleValue));
    }
}
