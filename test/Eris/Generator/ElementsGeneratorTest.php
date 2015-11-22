<?php
namespace Eris\Generator;

class ElementsGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->size = 10;
    }

    public function testGeneratesOnlyArgumentsInsideTheGivenArray()
    {
        $array = [1, 4, 5, 9];
        $generator = ElementsGenerator::fromArray($array);
        $generated = $generator($this->size);
        for ($i = 0; $i < 1000; $i++) {
            $this->assertContains(
                $generated,
                $array
            );
        }
    }

    public function testASingleValueCannotShrinkGivenThereIsNoExplicitRelationshipBetweenTheValuesInTheDomain()
    {
        $generator = ElementsGenerator::fromArray(['A', 2, false]);
        $this->assertSame(2, $generator->shrink(2));
    }

    public function testOnlyContainsTheElementsGeneratorOfTheGivenDomain()
    {
        $generator = ElementsGenerator::fromArray(['A', 2]);
        $this->assertFalse($generator->contains(1));
        $this->assertTrue($generator->contains('A'));
        $this->assertTrue($generator->contains(2));
        // disregarding types
        $this->assertTrue($generator->contains('2'));
    }

    /**
     * @expectedException DomainException
     */
    public function testExceptionWhenTryingToShrinkValuesOutsideOfTheDomain()
    {
        $generator = ElementsGenerator::fromArray(['A', 1]);
        $generator->shrink(2);
    }
}
