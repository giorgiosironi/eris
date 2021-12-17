<?php
namespace Eris;

class PHPUnitCommandTest extends \PHPUnit\Framework\TestCase
{
    public static function commandExamples()
    {
        return [
            [
                'Foo::testBar',
                'ERIS_SEED=42 vendor/bin/phpunit --filter \'Foo::testBar\''
            ],
            [
                'Foo\\Bar::testBaz',
                'ERIS_SEED=42 vendor/bin/phpunit --filter \'Foo\\\\Bar::testBaz\''
            ]
        ];
    }

    /**
     * @dataProvider commandExamples
     */
    public function testItCanComposeFrom($name, $fullString)
    {
        $command = PHPUnitCommand::fromSeedAndName(42, $name);

        $this->assertEquals($fullString, $command->__toString());
    }
}
