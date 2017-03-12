Composite generators
====================

These Generators implement the Composite pattern to wire together existing Generators and `callables`_. 

.. _frequency:

Frequency
---------

``Generator\frequency`` randomly chooses a Generator to use from the specified list, weighting the probability of each Generator with the provided value.

.. literalinclude:: ../../examples/FrequencyTest.php
   :language: php

``testFalsyValues`` chooses the ``false`` value half of the times, ``0`` one quarter of the time, and ``''`` one quarte of the time.

``testAlwaysFails`` chooses the Generator from 1 to 100 half of the times. However, in case of failure it will try to shrink the value only with the original Generator that created it. Therefore, each of the possible outputs will be possible:

.. code-block:: bash

    Failed asserting that 1 matches expected 0.
    Failed asserting that 100 matches expected 0.
    Failed asserting that 200 matches expected 0.

.. _oneof:

One Of
------

``Generator\oneOf`` is a special case of ``Generator\frequency`` which selects each of the specified Generators with the same probability.

.. literalinclude:: ../../examples/OneOfTest.php
   :language: php

.. seealso::

    :ref:`elements()<elements>` does the same with values instead of Generators.

.. _map:

Map
---

Map allows a Generator's output to be modified by applying the callable to the generated value.

.. literalinclude:: ../../examples/MapTest.php
   :language: php

``testApplyingAFunctionToGeneratedValues`` generates a vector of even numbers. Notice that any mapping can still be composed by other Generators: in this case, the even number Generator can be composed by ``Generator\vector()``, 

``testShrinkingJustMappedValues`` shows how shrinking respects the mapping function: running this test produces 102 as the minimal input that still makes the assertion fail. The underlying ``Generator\nat()`` shrinks number by decrementing them, but the mapping function is still applied so that only even numbers are passed to the ``then()``.

.. code-block:: bash

    1) MapTest::testShrinkingJustMappedValues
    The number is not less than 100
    Failed asserting that 102 is equal to 100 or is less than 100.

    /home/giorgio/code/eris/examples/MapTest.php:42
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:51
    /home/giorgio/code/eris/src/Eris/Shrinker/Random.php:68
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:128
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:53
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:130
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:158
    /home/giorgio/code/eris/examples/MapTest.php:43
    /home/giorgio/code/eris/examples/MapTest.php:43

    FAILURES!
    Tests: 1, Assertions: 254, Failures: 1.


``testShrinkingMappedValuesInsideOtherGenerators`` puts both examples together and generates a triple of even numbers, failing the test if their sum is greater than 100. The minimal failing example is a triple of number whose sum is 102.

.. code-block:: bash

    1) MapTest::testShrinkingMappedValuesInsideOtherGenerators
    The triple sum array (
      0 => 52,
      1 => 36,
      2 => 14,
    ) is not less than 100
    Failed asserting that 102 is equal to 100 or is less than 100.

    /home/giorgio/code/eris/examples/MapTest.php:62
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:51
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:130
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:158
    /home/giorgio/code/eris/examples/MapTest.php:63
    /home/giorgio/code/eris/examples/MapTest.php:63

    FAILURES!
    Tests: 1, Assertions: 216, Failures: 1.

.. _suchthat:

Such That
---------

Such That allows a Generator's output to be filtered, excluding values that to do not satisfy a condition.

.. literalinclude:: ../../examples/SuchThatTest.php
   :language: php

``testSuchThatBuildsANewGeneratorFilteringTheInnerOne`` generates a vector of numbers greater than 42. Notice that any filterting can still be composed by other Generators: in this case, the greater-than-42 number Generator can be composed by ``Generator\vector()``, 

``testFilterSyntax`` shows the ``Generator\filter()`` syntax, which is just an alias for ``Generator\suchThat()``. The order of the parameters requires to pass the callable first, for consistency with ``Generator\map()`` and in opposition to ``array_filter``.

``testSuchThatAcceptsPHPUnitConstraints`` shows that you can pass in PHPUnit constraints in lieu of callables, in the same way as they are passed to ``assertThat()``, or to ``with()`` when defining PHPUnit mock expectations.

``testSuchThatShrinkingRespectsTheCondition`` shows that shrinking takes into account the callable and stops when it is not satisfied anymore. Therefore, this test will fail for all numbers lower than or equal to 100, but the minimum example found is 43 as it's the smallest and simplest value that still satisfied the condition.

.. code-block:: bash

    1) SuchThatTest::testSuchThatShrinkingRespectsTheCondition
    $number was asserted to be more than 100, but it's 43
    Failed asserting that false is true.

    /home/giorgio/code/eris/examples/SuchThatTest.php:85
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:51
    /home/giorgio/code/eris/src/Eris/Shrinker/Random.php:68
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:128
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:53
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:130
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:158
    /home/giorgio/code/eris/examples/SuchThatTest.php:34
    /home/giorgio/code/eris/examples/SuchThatTest.php:34

``testSuchThatShrinkingRespectsTheConditionButTriesToSkipOverTheNotAllowedSet`` shows instead how shrinking does not give up easily, but shrinks the inner generator even more to see is simpler values may still satisfy the condition of being different from 42. Therefore, the test fails with the shrunk input 0, not 43 as before:

.. code-block:: bash

    1) SuchThatTest::testSuchThatShrinkingRespectsTheConditionButTriesToSkipOverTheNotAllowedSet
    $number was asserted to be more than 100, but it's 0
    Failed asserting that false is true.

    /home/giorgio/code/eris/examples/SuchThatTest.php:85
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:51
    /home/giorgio/code/eris/src/Eris/Shrinker/Random.php:68
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:128
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:53
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:130
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:158
    /home/giorgio/code/eris/examples/SuchThatTest.php:47
    /home/giorgio/code/eris/examples/SuchThatTest.php:47

.. _bind:

Bind
----

Bind allows a Generator's output to be used as an input to create another Generator. This composition allows to create several random values which are correlated with each other, by using the same input for their Generators parameters.

For example, here's how to create a vector along with a random element chosen by it.

.. literalinclude:: ../../examples/BindTest.php
   :language: php

.. _callables: http://php.net/manual/en/language.types.callable.php
