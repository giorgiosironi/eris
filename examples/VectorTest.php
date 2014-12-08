<?php
use Eris\BaseTestCase;

class VectorTest extends BaseTestCase
{
    public function testConcatenationMaintainsLength()
    {
        $this->forAll([
            $this->genVector($this->genNat()),
            $this->genVector($this->genNat()),
        ])
            ->__invoke(function($first, $second) {
                var_dump($first, $second);
                $concatenated = array_merge($first, $second);
                $this->assertEquals(
                    count($concatenated),
                    count($first) + count($second),
                    var_export($first, true) . " and " . var_export($second, true) . " do not maintain their length when concatenated."
                );
            });
    }
}
