<?php
namespace Eris\Generator;

class ElementsTest extends \PHPUnit_Framework_TestCase
{
    public function testGeneratesOnlyArgumentsInsideTheGivenArray()
    {
        $array = [1, 4, 5, 9];
        $generator = Elements::fromArray($array);
        $generated = $generator();
        for ($i = 0; $i < 1000; $i++) {
            $this->assertContains(
                $generated,
                $array
            );
        }
    }

    public function testASingleValueCannotShrinkGivenThereIsNoExplicitRelationshipBetweenTheValuesInTheDomain()
    {
        $generator = Elements::fromArray(['A', 2, false]);
        $this->assertEquals(2, $generator->shrink(2));
    }

    public function testOnlyContainsTheElementsOfTheGivenDomain()
    {
        $generator = Elements::fromArray(['A', 2]);
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
        $generator = Elements::fromArray(['A', 1]);
        $generator->shrink(2);
    }
}
