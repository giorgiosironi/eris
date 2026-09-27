<?php
/*
 * Type inference tests for the generic annotations of the Generators.
 *
 * This file is analysed by PHPStan (vendor/bin/phpstan) but never executed:
 * each assertType() call fails the analysis when PHPStan infers another type.
 */
namespace Eris\Types;

use Eris\Generator;
use Eris\Generators;
use Eris\Random\RandomRange;
use Eris\Random\RandSource;
use Eris\Sample;
use PHPUnit\Framework\Constraint\IsType;

use function PHPStan\Testing\assertType;

// Scalars
assertType('Eris\Generator\IntegerGenerator<int>', Generator\int());
assertType('Eris\Generator\IntegerGenerator<int<1, max>>', Generator\pos());
assertType('Eris\Generator\IntegerGenerator<int<0, max>>', Generator\nat());
assertType('Eris\Generator\IntegerGenerator<int<min, -1>>', Generator\neg());
assertType('Eris\Generator\ChooseGenerator', Generator\choose(0, 9));
assertType('Eris\Generator\ChooseGenerator', Generator\byte());
assertType('Eris\Generator\FloatGenerator', Generator\float());
assertType('Eris\Generator\BooleanGenerator', Generator\bool());
assertType('Eris\Generator\StringGenerator', Generator\string());
assertType('Eris\Generator\CharacterGenerator', Generator\char());
assertType('Eris\Generator\NamesGenerator', Generator\names());
assertType('Eris\Generator\RegexGenerator', Generator\regex('[a-z]{3}'));
assertType('Eris\Generator\DateGenerator', Generator\date());
assertType('Eris\Generator\ConstantGenerator<int>', Generator\constant(2));

// elements() takes either one array of elements or several elements
assertType('Eris\Generator\ElementsGenerator<int>', Generator\elements(1, 2, 3));
assertType('Eris\Generator\ElementsGenerator<int>', Generator\elements([1, 2, 3]));
assertType('Eris\Generator\ElementsGenerator<string>', Generator\elements('A', 'B', 'C'));
assertType('Eris\Generator\ElementsGenerator<array{int, int}>', Generator\elements([1, 2], [3, 4]));
assertType('Eris\Generator\ElementsGenerator<array{int, int}|int|string>', Generator\elements(10, 'hello-world', [1, 2]));

// Collections
assertType('Eris\Generator\SequenceGenerator<int>', Generator\seq(Generator\int()));
assertType('Eris\Generator\VectorGenerator<int<0, max>>', Generator\vector(3, Generator\nat()));
assertType('Eris\Generator\SetGenerator<int<0, max>>', Generator\set(Generator\nat()));
assertType('Eris\Generator\SubsetGenerator<string>', Generator\subset(['a', 'b', 'c']));
assertType('Eris\Generator\TupleGenerator<int|string>', Generator\tuple(Generator\int(), Generator\string()));
assertType('Eris\Generator\TupleGenerator<int|string>', Generator\tuple([Generator\int(), Generator\string()]));
assertType(
    'Eris\Generator\AssociativeArrayGenerator<string, int|string>',
    Generator\associative([
        'letter' => Generator\elements('A', 'B', 'C'),
        'cipher' => Generator\choose(0, 9),
    ])
);

// Composites
assertType(
    'Eris\Generator\OneOfGenerator<float|int<min, -1>|int<1, max>>',
    Generator\oneOf(Generator\pos(), Generator\neg(), Generator\float())
);
assertType(
    'Eris\Generator\FrequencyGenerator<int>',
    Generator\frequency([8, Generator\choose(1, 100)], [4, Generator\choose(100, 200)])
);
assertType('Eris\Generator\FrequencyGenerator<bool|int|string>', Generator\frequency([8, false], [4, 0], [4, '']));
assertType(
    'Eris\Generator\MapGenerator<int<0, max>, DateTimeImmutable>',
    Generator\map(function (int $n): \DateTimeImmutable {
        return new \DateTimeImmutable('@' . $n);
    }, Generator\nat())
);
assertType(
    'Eris\Generator\BindGenerator<int<0, max>, list<string>>',
    Generator\bind(Generator\nat(), function (int $n) {
        return Generator\vector($n, Generator\string());
    })
);
assertType(
    'Eris\Generator\SuchThatGenerator<int>',
    Generator\suchThat(function (int $n): bool {
        return $n > 42;
    }, Generator\int())
);
assertType('Eris\Generator\SuchThatGenerator<int>', Generator\filter(new IsType('int'), Generator\int()));

// The static methods of Generators are annotated like the functions
assertType('Eris\Generator\IntegerGenerator<int<0, max>>', Generators::nat());
assertType('Eris\Generator\ElementsGenerator<int>', Generators::elements([1, 2, 3]));
assertType('Eris\Generator\SequenceGenerator<int>', Generators::seq(Generators::int()));
assertType('Eris\Generator\TupleGenerator<int|string>', Generators::tuple(Generators::int(), Generators::string()));
assertType('Eris\Generator\OneOfGenerator<int<min, -1>|int<1, max>>', Generators::oneOf(Generators::pos(), Generators::neg()));

// Generated values
$rand = new RandomRange(new RandSource());
assertType('int<0, max>', Generators::nat()(10, $rand)->unbox());
assertType('list<int>', Generators::seq(Generators::int())(10, $rand)->unbox());
assertType('list<int<0, max>>', Sample::of(Generators::nat(), $rand)->repeat(10)->collected());
