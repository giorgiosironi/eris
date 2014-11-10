<?php

class ListConcatenationTest extends \PHPUnit_Framework_TestCase
{
    public function testLengthIsConserved()
    {
        $first = [rand(), rand()];
        $second = [rand(), rand(), rand()];
        $this->assertEquals(
            count($first) + count($second),
            count(array_merge($first, $second))
        );
    }

    
}
