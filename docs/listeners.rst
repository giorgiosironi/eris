Listeners
=========

Eris provide the possibility to pass in Listeners to be notified of events happening during a test run.

Listeners implement the ``Eris\Listener`` interface and are advised to extend the ``Eris\EmptyListener`` abstract base class to get an empty implementation for all the methods corresponding to events they don't need to listen to.

Consider that Eris performs (by default) 100 iterations for each ``forAll()`` instance, each corresponding to a different set of generated values. The following methods can be overridden to receive an event:

* ``startPropertyVerification()`` is called before the first iteration starts.
* ``endPropertyVerification($ordinaryEvaluations, $iterations, Exception $exception = null)`` is called when no more iterations will be performed, both in the case of test success and failure. The ``$ordinaryEvaluations`` parameter provides the actual number of evaluations performed. This number may be less than than the number of target ``$iterations`` due to failures or ``when()`` filters not being satisfied. The ``$exception``, when not null, indicated that the test has finally failed and corresponds to the error that is actually bubbling up rather than the original, unshrunk error.
* ``newGeneration(array $generation, $iteration)`` is called after generating a new iteration, and is passed the tuple of values along with the 0-based index of the iteration.
* ``failure(array $generation, Exception $e)`` is called after the failure of an assertion (and not for generic exceptions). The method can be called only once per ``then()`` run, and is called before any shrinking takes place.
* ``shrinking(array $generation)`` is called before each shrinking attempt, with the values that will be used as the simplified input.

``$generation`` is always an array of the same form as the arguments passed to ``then()``, without any Eris class wrapping them.

Collect Frequencies
-------------------

The ``collectFrequencies()`` Listener allows to gather all generated values in order to display their statistical distribution.

.. literalinclude:: ../examples/CollectTest.php
   :language: php

``testGeneratedDataCollectionOnScalars`` collects integers:

.. code-block:: bash

    12%  -1
    6%  -2
    4%  -5
    4%  -18
    4%  -11
    3%  -4
    ...

``testGeneratedDataCollectionOnMoreComplexDataStructures`` shows how by default more complex structures are encoded into a JSON value, to be used as the bin key in the map of values to counters:

.. code-block:: bash

    1%  [[-19,-16],"m"]
    1%  [[-3,-30],";"]
    1%  [[-9,1],"\f"]
    1%  [[-7,-1],"P"]
    1%  [[-1,-9],"^"]
    1%  [[1,18],"8"]
    1%  [[-53,-1],"."]
    ...

``testGeneratedDataCollectionWithCustomMapper`` shows how to provide a custom callable to map the generated values into a bin key. Arguments are passed to the callable in the same way as ``then()``. In this example, we are discovering that 10% of the generated arrays have length 3.

.. code-block:: bash

    39%  0
    26%  1
    10%  3
    5%  4
    5%  2
    4%  5
    3%  7
    3%  6
    3%  8
    1%  10
    1%  9

Log
---

The ``log()`` Listener allows to write a log file while particularly long tests are executing, showing the partial progress of the test.

.. literalinclude:: ../examples/LogFileTest.php
   :language: php

A file will be written during the test run with the following contents:

.. code-block:: bash

    ...
    [2016-03-24T09:14:20+00:00][2593] iteration 12: [-9]
    [2016-03-24T09:14:20+00:00][2593] iteration 13: [-59]
    [2016-03-24T09:14:20+00:00][2593] iteration 14: [-51]
    [2016-03-24T09:14:20+00:00][2593] iteration 15: [-52]
    [2016-03-24T09:14:20+00:00][2593] iteration 16: [-83]
    [2016-03-24T09:14:20+00:00][2593] iteration 17: [78]
    [2016-03-24T09:14:20+00:00][2593] failure: [78]. Failed asserting that 78 is equal
    to 42 or is less than 42.
    [2016-03-24T09:14:20+00:00][2593] shrinking: [77]
    [2016-03-24T09:14:20+00:00][2593] shrinking: [76]
    [2016-03-24T09:14:20+00:00][2593] shrinking: [75]
    [2016-03-24T09:14:20+00:00][2593] shrinking: [74]
    [
    ...

It is not advised to rely on this format for parsing, being it only oriented to human readability.

Minimum Evaluations
---

The ``minimumEvaluations($ratio)`` API method instantiates and wires in a Listener that checks that at least ``$ratio`` of the total number of inputs being generated is actually evaluated. This Listener is only needed in case of an aggressive use of ``when()``.

Management of this Listener is provided through this method instead of explicitly adding a Listener object, as there is a default Listener instantiated with a threshold of 0.5 that has to be replaced in case a new minimum is chosen.

.. literalinclude:: ../examples/MinimumEvaluationsTest.php
   :language: php

Both tests generate inputs in the range from 0 to 100, and since the condition of them being greater than 90 is rare, most of them will be discarded. By default Eris will check that 50% of the inputs are actually evaluated; therefore ``testFailsBecauseOfTheLowEvaluationRatio`` will fail with this message:

.. code-block:: bash

    ...
    There was 1 error:

    1) MinimumEvaluationsTest::testFailsBecauseOfTheLowEvaluationRatio
    OutOfBoundsException: Evaluation ratio 0.05 is under the threshold 0.5
    ...

The actual ratio may vary depending on the inputs being generated and may not be ``0.05``.

In `testPassesBecauseOfTheArtificiallyLowMinimumEvaluationRatio`, we accept a lower minimum evaluation ratio of 1%; therefore the test does not ordinarily fail. Its coverage will still be very poor, so the user is advised to precisely specify the inputs rather than generating a lot of them and discarding a large percentage with ``when()``.
