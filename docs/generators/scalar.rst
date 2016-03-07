Scalar generators
=================

Integers
--------

Integers can be generated, and by default they can be positive, or negative.
You can force the sign of a number with:

* ``Generator\nat()`` which produces an integer >= 0.
* ``Generator\pos()`` which produces an integer > 0.
* ``Generator\neg()`` which produces an integer < 0.

.. literalinclude:: ../../examples/IntegerTest.php
   :language: php

