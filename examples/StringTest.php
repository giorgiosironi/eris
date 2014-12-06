<?php
use Eris\BaseTestCase;

function concatenation($first, $second)
{
    if (strstr($second, 0)) {
        $second .= '...';
    }
    return $first . $second;
}

class StringTest extends BaseTestCase
{
    public function testRightIdentityElement()
    {
        $this->forAll([
            $this->genString(),
        ])
            ->__invoke(function($string) {
                $this->assertEquals(
                    $string,
                    concatenation($string, ''),
                    "Concatenating $string to ''"
                );
            });
    }
}
