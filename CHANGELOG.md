# Changelog

All notable changes to this project will be documented in this file.

The project follows [semantic versioning](http://semver.org/). `BC` stands for a change that impacts `Backward Compatibility`.

## [Unreleased]

## [0.13.0]
### Added
* `Eris\Generators` contains all the generator constructors as static methods.
* Support for PHP 8.1
### Changed
* The generator constructors implemented as simple function now call the static methods of `Eris\Generators`. They will be deprecated.
### Removed
* Support for PHP < 7.1
* Support for PHPUnit < 6

## [0.12.1] - 2021-07-27
### Changed
* Visibility of TestTrait's methods in Facade is now public (#110,#141). Thanks, @bekh6ex.
### Fixed
* Compatibility with newer versions of PHPUnit: `getAnnotations` is removed (#143). Thanks, @aszenz.

## [0.12.0] - 2021-03-25

* PHP 7.3 support (#120).
* PHP 7.4 support (#125).
* CI support for testing end to end suite also with Phpunit 8.x and 9.x 
* Allow `shrink()` to receive GeneratedValueOptions (#127).
* Allow specifying generator size with `sample()` (#128)
* Fix `BooleanGenerator::shrink()` to return a GeneratedValueSingle (#131)
* Drop support for HHVM 3.30

## [0.11.0] - 2018-09-16

* PHP 7.2 support (#116, #114).
* Annotations support: `@eris-method`, `@eris-shrink`, `@eris-ratio`, `@eris-repeat`, `@eris-duration`

## [0.10.0] - 2018-03-23

* Allowing use outside of PHPUnit through `Eris\Facade`.
* Fixed bug: `suchThat()` fails to generated good values when all those from generator size 0 are exclude (#100).
* PHPUnit 7.x support (#112, #113).
* BC: dropped the deprecated `Shrinker\Random`.
* BC: dropped the unused `Generator::contains()`.

## [0.9.0] - 2017-03-12

* Using new `multiple` deterministic shrinking instead of `random`, being abandoned (#87).
* Supporting PHPUnit 6.x (#96).
* Supporting PHP 7.1 (#97).
* Added `Listener::onAttempt()`
* Fixed bug: `pos()` and `neg()` can shrink to `0` (#96).
* Fixed bug: denominator in float generation can be 0 (#92).
* Fixed bug: shrinking of date generation uses wrong operator precedence (#94).
* Fixed bug: reproducible PHPUnit commands are not escaped correctly if they contain namespaced classes.
* Added CONTRIBUTORS
* BC: `minimumEvaluationRatio` is now a method to be called, not a private field. Defaults to 0.5.
* BC: `GeneratedValue` is now an interface and not a class.
* BC: extended `Listener::endPropertyVerification()` with additional parameters `$iterations` and optional `$exception`.

## [0.8.0] - 2016-04-15

* Updated dependency on `icomefromthenet/reverse-regex` to solve warnings on PHP 7.
* `bind` Generator.
* Default string dump for `Listener\collectFrequencies()`.
* Optionally logging generations with `hook(Listener\log($filename))`.
* `disableShrinking()` option.
* `limitTo()` accepts a `DateInterval` too.
* Configurability of randomness: choice between `rand`, `mt_rand`, and a pure PHP Mersenne Twister.
* `suchThat` Generator accepts PHPUnit constraints like `when()`.
* `Generator\constant()` utility function.
* Fixed bug of size not being fully explored due to slow growth.
* Switched to PSR-2 coding standards and PSR-4 autoloading.
* BC: `frequency` generator only accepts variadics args, not an array anymore.
* BC: removed `strictlyPos` and `strictlyNeg` Generators as duplicated of `pos` and `neg` ones.
* BC: removed `andAlso`, `theCondition`, `andTheCondition`, `implies`, `imply` aliases which no one uses. Added `and` for multiple preconditions.

## [0.7.0] - 2016-03-04

* `associative`, `map`, `subSet`, `suchThat` Generators.
* Optionally limiting the number of generations with `limitTo()`.
* Optionally collecting generated data with `hook(Listener\collectFrequencies())`.
* Support for listeners with `startPropertyVerification`, `newGeneration` and `endPropertyVerification` events.
* BC: changed Generators API to use `GeneratedValue` objects.
* BC: requiring PHP 5.5 or newer.
* BC: dropped array single parameter in `forAll()`.
* PHP 7 compatibility.
* Renaming all Generator classes to *Generator.

## [0.5.0] - 2015-11-05

* Generators are now based on size, an increasing random parameter.
* `choose()` new Generator to get integers inside a fixed range.
* PHPUnit 5.x is supported.
* `set` new Generator.
* Differentiating `pos`, `nat`, `neg` Generators.
* GeneratorSampleTest to get sample output from Generators.

## [0.4.0] - 2015-05-27

* Showing generated input with `ERIS_ORIGINAL_INPUT=1`.
* `names` and `date` (DateTime) new Generators.
* `tuple` Generator supports variadic arguments.
* Shrinking respects `when()` clauses.
* Dates and sorting examples.

## [0.3.1] - 2015-01-07

* `forAll()` accepts multiple arguments instead of an array.
* `byte` Generator.

## [0.3.0] - 2014-12-28

* `frequency` and `oneOf` Generators that combine other Generators. 
* `sequence` Generator for lists of constant type and variable size.
* `char` Generators with `printableCharacter` Antecedent.
* `int`, `pos`, `neg` and `float` Generators.
* `regex` Generator to build strings satisfying a regular expression.
* Shrinking respects an optional maximum time limit (`$this->shrinkingtimeLimit`).

## [0.2.0] - 2014-12-13

* Generators now use the Generator\nat(...) syntax.
* Added `bool`, `constant`, `elements`, `string`, `tuple` Generators.
* Improved shrinking by performing it on all involved Generators.
* Reproducibility with ERIS_SEED.

## [0.1.0] - 2014-12-08

* `forAll()` and `then()` syntax.
* `natural` and `vector` Generators.
* `when()` for constraints.
* Basic best-effort shrinking.
* `sample()` and `sampleShrink()` for Generators.

[Unreleased]: https://github.com/giorgiosironi/eris/compare/0.13.0...HEAD
[0.13.0]: https://github.com/giorgiosironi/eris/compare/0.12.1...0.13.0
[0.12.1]: https://github.com/giorgiosironi/eris/compare/0.12.0...0.12.1
[0.12.0]: https://github.com/giorgiosironi/eris/compare/0.11.0...0.12.0
[0.11.0]: https://github.com/giorgiosironi/eris/compare/0.10.0...0.11.0
[0.10.0]: https://github.com/giorgiosironi/eris/compare/0.9.0...0.10.0
[0.9.0]: https://github.com/giorgiosironi/eris/compare/0.8.0...0.9.0
[0.8.0]: https://github.com/giorgiosironi/eris/compare/0.7.0...0.8.0
[0.7.0]: https://github.com/giorgiosironi/eris/compare/0.5.0...0.7.0
[0.5.0]: https://github.com/giorgiosironi/eris/compare/0.4.0...0.5.0
[0.4.0]: https://github.com/giorgiosironi/eris/compare/0.3.1...0.4.0
[0.3.1]: https://github.com/giorgiosironi/eris/compare/0.3.0...0.3.1
[0.3.0]: https://github.com/giorgiosironi/eris/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/giorgiosironi/eris/compare/0.1.0...0.2.0
[0.1.0]: https://github.com/giorgiosironi/eris/releases/0.1.0
