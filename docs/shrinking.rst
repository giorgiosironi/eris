Shrinking
=========

When one of the generated examples makes a test fail, it is useful for debugging purposes to try and generate the simplest possible input that still triggers this failure.

Eris, like all QuickCheck implementations, performs a process called shrinking which:

- stops each test at the first failure
- asks the Generator to turn the currently generated value to another, simpler value
- perform the test with the new value.

Shrinking repeats this process until the test does not fail anymore, or the value cannot be simplified further. The last input in the shrinking sequence that still makes the test fail is the one reported to the user, while all other values are regarded as more complex and thrown away.

Simplest example
----------------

.. literalinclude:: ../examples/ShrinkingTest.php
   :language: php

``testShrinkingAString`` is the simplest shrinking example. Each iteration generates random strings and test them to check that they do not contain the letter ``B``. This is an example sequence of generated values (which by default will change at every run):

.. code-block:: php

    string(0) ""
    string(1) "K"
    string(2) "g,"
    string(3) "=%,"
    string(7) "jGHr38i"
    string(15) "L(uw^K)/&hf!mQK"
    string(9) ":W}W[+<GR"
    string(20) ":e|$dI,[Bj(Kx-4`-"3X"
    string(19) ":e|$dI,[Bj(Kx-4`-"3"
    string(18) ":e|$dI,[Bj(Kx-4`-""
    string(17) ":e|$dI,[Bj(Kx-4`-"
    string(16) ":e|$dI,[Bj(Kx-4`"
    string(15) ":e|$dI,[Bj(Kx-4"
    string(14) ":e|$dI,[Bj(Kx-"
    string(13) ":e|$dI,[Bj(Kx"
    string(12) ":e|$dI,[Bj(K"
    string(11) ":e|$dI,[Bj("
    string(10) ":e|$dI,[Bj"
    string(9) ":e|$dI,[B"
    string(8) ":e|$dI,["

All the values up to ``string(9) ":W}W[+<GR"`` pass the test. The value ``string(20) ":e|$dI,[Bj(Kx-4`-"3X"`` is the first to fail.

From there, the value is shrunk by chopping away a single character at the end of the string. The value ``string(8) ":e|$dI,["`` is the first one in the shrinking sequence that does not fail the test, so the process stops there. The last simplified value to still fail the test is ``string(9) ":e|$dI,[B"`` and it's the one presented to the user:

.. code-block:: bash

    1) ShrinkingTest::testShrinkingAString
    Failed asserting that ':e|$dI,[B' does not contain "B".

    /home/giorgio/code/eris/examples/ShrinkingTest.php:16
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:51
    /home/giorgio/code/eris/src/Eris/Shrinker/Random.php:68
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:128
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:53
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:130
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:158
    /home/giorgio/code/eris/examples/ShrinkingTest.php:17
    /home/giorgio/code/eris/examples/ShrinkingTest.php:17

    FAILURES!
    Tests: 1, Assertions: 119, Failures: 1.

Shrinking and preconditions
--------------------------

``testShrinkingRespectsAntecedents`` generates a random number from 0 to 20 and tries to check that it is multiple of 29. All generated numbers will fail this test, but shrinking will try to present the lowest possible number; still, the ``when()`` antecedent has to be satisfied and so the number cannot decrease down to 0 but has to stop at 11:

.. code-block:: bash

    1) ShrinkingTest::testShrinkingRespectsAntecedents
    The number 11 is not multiple of 29
    Failed asserting that false is true.

    /home/giorgio/code/eris/examples/ShrinkingTest.php:18
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:51
    /home/giorgio/code/eris/src/Eris/Shrinker/Random.php:68
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:128
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:53
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:130
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:158
    /home/giorgio/code/eris/examples/ShrinkingTest.php:19
    /home/giorgio/code/eris/examples/ShrinkingTest.php:19

    FAILURES!
    Tests: 1, Assertions: 4, Failures: 1.

Shrinking is only performed when assertions fail: generic exceptions bubbling up out of the ``then()`` will just interrupt the test.


Shrinking time limit
--------------------

You can set a time limit for shrinking if you prefer to be presented with more complex examples with respect to spending test suite running time:

.. literalinclude:: ../examples/ShrinkingTest.php
   :language: php

The shrinking for this test will not run for more than 2 seconds (although the test as a whole may take more):

.. code-block:: bash

    1) ShrinkingTimeLimitTest::testLengthPreservation
    RuntimeException: Eris has reached the time limit for shrinking (2s elapsed of 2s), here it is presenting the simplest failure case.
    If you can afford to spend more time to find a simpler failing input, increase it with $this->shrinkingTimeLimit($seconds).

    /home/giorgio/code/eris/src/Eris/Shrinker/Random.php:71
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:128
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:53
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:130
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:158
    /home/giorgio/code/eris/examples/ShrinkingTimeLimitTest.php:32
    /home/giorgio/code/eris/examples/ShrinkingTimeLimitTest.php:32

    Caused by
    PHPUnit_Framework_ExpectationFailedException: Concatenating 'hW4N*:fD0&%+D_' to 'p:\(,N\7A6' gives 'hW4N*:fD0&%+D_p:\(,N\7A6ERROR'

    Failed asserting that 29 matches expected 24.

    /home/giorgio/code/eris/examples/ShrinkingTimeLimitTest.php:31
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:51
    /home/giorgio/code/eris/src/Eris/Shrinker/Random.php:68
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:128
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:53
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:130
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:158
    /home/giorgio/code/eris/examples/ShrinkingTimeLimitTest.php:32
    /home/giorgio/code/eris/examples/ShrinkingTimeLimitTest.php:32

    FAILURES!
    Tests: 1, Assertions: 8, Errors: 1.

Tree-based shrinking
--------------------

- for some generators what goes on under the hood is not a linear shrinking, write test that demonstrates that with Sample class
-- optimistic path
-- pessimistic path
-- average path (choose the middle)

Disabling shrinking
-------------------

In some cases the ``then()`` method is non-deterministic as it spawns other processes or talks to other services. Moreover, ``then()`` can be very slow to execute when targeting APIs for end-to-end tests. Finally, if it performs any cleanup executing it for shrinking may clean lods or databases traces from the actual test failure, preventing effective debugging.

Therefore, it is possible to configure Eris to disable the shrinking process. As a result, the first assertion failure will stop the test and let the exception bubble up:

.. literalinclude:: ../examples/DisableShrinkingTest.php
   :language: php

This test will show a failure message containing ``Total calls: 1``.
