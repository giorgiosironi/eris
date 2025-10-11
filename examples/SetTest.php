<?php

use Eris\Generators;
use Eris\TestTrait;

class SetTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function testSetsOfAnotherGeneratorsDomain(): void
    {
        $this->forAll(
            Generators::set(Generators::nat())
        )
            ->then(function ($set): void {
                self::assertIsArray($set);
                foreach ($set as $element) {
                    $this->assertGreaterThanOrEqual(0, $element);
                }
            });
    }
}
