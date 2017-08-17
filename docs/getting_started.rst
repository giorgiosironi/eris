Getting started
===============

This test tries to verify that natural numbers from 0 to 1000 are all smaller than 42. It's a failing test designed to show you an example of error message.

.. literalinclude:: ../examples/ReadmeTest.php
   :language: php

Eris generates a sample of elements from the required domain (here the integers from 0 to 1000) and verifies a property on each of them, stopping at the first failure. Its functionalities are exported trough a ``TestTrait`` you can insert into your PHPUnit tests and through a series of functions in the ``Eris\Generator`` and ``Eris\Listener`` namespaces.

Generators implement the ``Eris\Generator`` interface, and provide random generation of values conforming to some types or domains. By combining them, your System Under Test can receive hundreds of different inputs with only a few lines of code.

Given that the input is unknown when writing the test, we have to test predicates over the result or the state of the System Under Test instead of writing equality assertions over the output. Properties should always be true, so that their violation indicates a bug and hence a failing test.

.. code-block:: bash

    [10:34:32][giorgio@Bipbip:~/code/eris]$ vendor/bin/phpunit examples/ReadmeTest.php
    PHPUnit 4.3.5 by Sebastian Bergmann.

    Configuration read from /home/giorgio/code/eris/phpunit.xml

    F

    Time: 234 ms, Memory: 3.25Mb

    There was 1 failure:

    1) ReadmeTest::testNaturalNumbersMagnitude
    42 is not less than 42 apparently
    Failed asserting that false is true.

    /home/giorgio/code/eris/examples/ReadmeTest.php:15
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:48
    /home/giorgio/code/eris/src/Eris/Quantifier/RoundRobinShrinking.php:45
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:69
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:50
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:71
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:87
    /home/giorgio/code/eris/examples/ReadmeTest.php:16
    /home/giorgio/code/eris/examples/ReadmeTest.php:16

    FAILURES!
    Tests: 1, Assertions: 826, Failures: 1.

Eris also tries to :doc:`shrink<shrinking>` the input after a failure, giving you the simplest input that still fails the test. In this example, the original input was probably something like ``562``, but Eris tries to make it smaller until the test became green again. The smallest value that still fails the test is the one presented to you.

.. explain more of the actual process: stops at the first failure of the 100-element sample
