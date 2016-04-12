.. _reproducibility:

Reproducibility
===============

Eris allows you to seed the pseudorandom number generator in order to attempt to reproduce the same test run and check if a previously found bug has now been fixed.

Consider this test:

.. literalinclude:: ../examples/AlwaysFailTest.php
   :language: php

This test will fail, no matter which value is generated. No shrinking will be performed as the selected Generator considers the elements of equal complexity.

When you run this test, you may obtain an output very similar to:

.. code-block:: bash

    F                                                                   1 / 1 (100%)
    Reproduce with:
    ERIS_SEED=1458646953837419 vendor/bin/phpunit --filter AlwaysFailsTest::testFailsNoMatterWhatIsTheInput


    Time: 44 ms, Memory: 3.50Mb

    There was 1 failure:

    1) AlwaysFailsTest::testFailsNoMatterWhatIsTheInput
    This test fails by design. 'd' was passed in

    /home/giorgio/code/eris/examples/AlwaysFailsTest.php:15
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:51
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:128
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:156
    /home/giorgio/code/eris/examples/AlwaysFailsTest.php:16
    /home/giorgio/code/eris/examples/AlwaysFailsTest.php:16

    FAILURES!
    Tests: 1, Assertions: 0, Failures: 1.

If you take the suggested command line and execute it, you will see the same error message, selecting ``d`` as the random input:

.. code-block:: bash

    $ ERIS_SEED=1458646953837419 vendor/bin/phpunit --filter AlwaysFailsTest::testFailsNoMatterWhatIsTheInput
    PHPUnit 5.0.9 by Sebastian Bergmann and contributors.

    F                                                                   1 / 1 (100%)
    Reproduce with:
    ERIS_SEED=1458646953837419 vendor/bin/phpunit --filter AlwaysFailsTest::testFailsNoMatterWhatIsTheInput


    Time: 130 ms, Memory: 10.75Mb

    There was 1 failure:

    1) AlwaysFailsTest::testFailsNoMatterWhatIsTheInput
    This test fails by design. 'd' was passed in

    /home/giorgio/code/eris/examples/AlwaysFailsTest.php:15
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:51
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:128
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:156
    /home/giorgio/code/eris/examples/AlwaysFailsTest.php:16
    /home/giorgio/code/eris/examples/AlwaysFailsTest.php:16

    FAILURES!
    Tests: 1, Assertions: 0, Failures: 1.

Running the test without a ``ERIS_SEED`` environment variable will restore the previous behavior, exploring the Generator space in search of brand new values.
