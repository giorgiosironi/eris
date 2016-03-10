Runtime limits
==============

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
