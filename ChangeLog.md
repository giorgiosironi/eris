# ChangeLog

## 0.3.1

* `forAll()` accepts multiple arguments instead of an array.
* `byte` Generator.

## 0.3.0

* `frequency` and `oneOf` Generators that combine other Generators. 
* `sequence` Generator for lists of constant type and variable size.
* `char` Generators with `printableCharacter` Antecedent.
* `int`, `pos`, `neg` and `float` Generators.
* `regex` Generator to build strings satisfying a regular expression.
* Shrinking respects an optional maximum time limit (`$this->shrinkingtimeLimit`).

## 0.2.0

* Generators now use the Generator\nat(...) syntax.
* Added `bool`, `constant`, `elements`, `string`, `tuple` Generators.
* Improved shrinking by performing it on all involved Generators.
* Reproducibility with ERIS_SEED.

## 0.1.0

* `forAll()` and `then()` syntax.
* `natural` and `vector` Generators.
* `when()` for constraints.
* Basic best-effort shrinking.
* `sample()` and `sampleShrink()` for Generators.
