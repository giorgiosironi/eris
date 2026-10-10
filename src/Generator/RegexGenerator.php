<?php
namespace Eris\Generator;

use BadFunctionCallException;
use InvalidArgumentException;
use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;
use ReverseRegex\Lexer;
use ReverseRegex\Random\GeneratorFactory;
use ReverseRegex\Parser;
use ReverseRegex\Generator\Scope;

/**
 * Note * and + modifiers are unbounded: the maximum number of repetitions
 * is set by $unboundedMax, or by the default of the reverse-regex library
 * when it is null.
 *
 * @param string $expression
 * @param int|null $unboundedMax maximum number of repetitions for * and +
 * @param string $random one of 'srand', 'mersenne' or 'simple': reverse-regex random generator to use
 * @return Generator\RegexGenerator
 */
function regex($expression, $unboundedMax = null, $random = 'simple')
{
    return Generators::regex($expression, $unboundedMax, $random);
}

/**
 * @template-implements Generator<string>
 */
class RegexGenerator implements Generator
{
    private $expression;
    private ?int $unboundedMax;
    private string $random;

    /**
     * @param string $expression
     * @param int|null $unboundedMax maximum number of repetitions for * and +
     * @param string $random one of 'srand', 'mersenne' or 'simple': reverse-regex random generator to use,
     *        seeded from Eris's random source
     */
    public function __construct($expression, ?int $unboundedMax = null, string $random = 'simple')
    {
        if (!class_exists("ReverseRegex\Parser")) {
            throw new BadFunctionCallException("Please install the suggested dependency ilario-pierbattista/reverse-regex to run this Generator.");
        }
        if (!in_array($random, ['srand', 'mersenne', 'simple'], true)) {
            throw new InvalidArgumentException("Unknown random generator '{$random}': use 'srand', 'mersenne' or 'simple'.");
        }
        $this->expression = $expression;
        $this->unboundedMax = $unboundedMax;
        $this->random = $random;
    }

    public function __invoke($_size, RandomRange $rand)
    {
        $lexer = new Lexer($this->expression);
        $gen   = (new GeneratorFactory())->create($this->random, $rand->rand());
        $result = null;

        $parser = new Parser($lexer, new Scope(), new Scope());
        if ($this->unboundedMax !== null) {
            $parser->setUnboundedMax($this->unboundedMax);
        }
        $parser->parse()->getResult()->generate($result, $gen);

        return GeneratedValueSingle::fromJustValue($result, 'regex');
    }

    public function shrink(GeneratedValue $value)
    {
        return $value;
    }
}
