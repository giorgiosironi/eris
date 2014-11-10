<?php

class ListConcatenationTest extends \PHPUnit_Framework_TestCase
{
    public function testLengthIsConserved()
    {
        $first = [1, 2];
        $second = [3, 4, 5];
        $this->assertEquals(
            count($first) + count($second),
            count(array_merge($first, $second))
        );
    }
}
