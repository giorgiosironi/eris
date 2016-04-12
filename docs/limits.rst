Runtime limits
==============

Time and iterations
-------------------

By default Eris extracts a sample of 100 values for each ``forAll()`` call, and runs the ``then()`` callback over each of them.

For tests which take very long to run, it is possible to either limit the number of elements in the sample, or to specify a time limit the test should not exceed. For this purpose, the ``limitTo()`` method accepts either:

* an integer requesting a fixed number of iterations;
* a `DateInterval`_ object from the standard PHP library.
 
.. literalinclude:: ../examples/LimitToTest.php
   :language: php
 
In the first example, the test is stopped after 5 generations.

The second example is about a future feature, not implemented yet, which will make it possible to specify a time limit while requiring a minimum number of operations.

In the third example, a time limit of 2 seconds is specified. Whenever a new element has to be added to the sample, the time limit is checked to see if the elapsed time from the start of the test has exceeded it.

Since it is possible for the generation process to have some overhead, the time specified is not an hard limit but will only be approximately respected. More precisely, the iteration running when the time limit is reached still has to be finished without being interrupted, along with any shrinking process derived from its potential failure.

.. _DateInterval: http://php.net/dateinterval

Size of generated data
----------------------

Many Generators accept a ``size`` parameter that should be used as an upper bound when creating new random elements. For example, this bound corresponds to a maximum positive integer, or to the maximum number of elements inside an array.

.. literalinclude:: ../examples/SizeTest.php
   :language: php

By default size is equal to ``1000``, which means no number greater than ``1000`` in absolute value will be generated. This test sets the maximum size to ``1,000,0000``, and naturally fails when a number greater than ``100,000`` is picked and passed to the assertion. The failure message shows the shrunk input, exactly ``100,000``:

.. code-block:: bash

    There was 1 failure:

    1) SizeTest::testMaxSizeCanBeIncreased
    Failed asserting that 100000 is less than 100000.

    /home/giorgio/code/eris/examples/SizeTest.php:21
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:51
    /home/giorgio/code/eris/src/Eris/Shrinker/Random.php:68
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:126
    /home/giorgio/code/eris/src/Eris/Quantifier/Evaluation.php:53
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:128
    /home/giorgio/code/eris/src/Eris/Quantifier/ForAll.php:156
    /home/giorgio/code/eris/examples/SizeTest.php:22
    /home/giorgio/code/eris/examples/SizeTest.php:22

The maximum sizes that can be reached are also limited by the :ref:`underlying random number generator<randomness-size>`. 

