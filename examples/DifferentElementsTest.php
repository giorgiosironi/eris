<?php
use Eris\Generator;
use Eris\TestTrait;

class Type
{
    const TYPE_A = 1;
    const TYPE_B = 2;
    const TYPE_C = 3;

    private $type;

    private function __construct($type)
    {
        $this->type = $type;
    }

    public static function A()
    {
        return new self(self::TYPE_A);
    }

    public static function B()
    {
        return new self(self::TYPE_B);
    }

    public static function C()
    {
        return new self(self::TYPE_C);
    }
}

class DifferentElementsTest extends \PHPUnit_Framework_TestCase
{
    use TestTrait;

    /**
     * @test
     */
    public function a_type_is_different_than_another_one()
    {
        $allTypes = [
            Type::A(),
            Type::B(),
            Type::C(),
        ];
        $remove = function ($array, $whatToRemove) {
            return array_values(array_filter(
                $array,
                function ($candidate) use ($whatToRemove) {
                    return $candidate != $whatToRemove;
                }
            ));
        };

        $this
            ->forAll(Generator\bind(
                call_user_func_array('Eris\Generator\elements', $allTypes),
                function ($first) use ($allTypes, $remove) {
                    return Generator\tuple(
                        Generator\constant($first),
                        Generator\elements($remove($allTypes, $first))
                    );
                }
            ))
            ->then(function ($differentElements) {
                $this->assertNotEquals($differentElements[0], $differentElements[1], "Several discussion types are equals");
            });
    }
}
