<?php
namespace Eris\Generator;

use Eris\Generator;
use Eris\Random\RandomRange;

/**
 * @return StringGenerator
 */
function string()
{
    return StringGenerator::string();
}

class StringGenerator implements Generator
{
    public function __invoke($size, RandomRange $rand)
    {
        $length = $rand->rand(0, $size);

        $built = '';
        for ($i = 0; $i < $length; $i++) {
            $built .= chr($rand->rand(33, 126));
        }
        return GeneratedValueSingle::fromJustValue($built, 'string');
    }

    public function shrink(GeneratedValue $element)
    {
        if ($element->unbox() === '') {
            return $element;
        }
        return GeneratedValueSingle::fromJustValue(
            substr($element->unbox(), 0, -1),
            'string'
        );
    }

    /**
     * @return StringGenerator
     */
    public static function string()
    {
        return new self();
    }
}
