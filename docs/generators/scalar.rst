Scalar generators
=================

.. _nat:
.. _pos:
.. _neg:
.. _byte:
.. _choose:

Integers
--------

Integers can be generated, and by default they can be positive, or negative.
You can force the sign of a number with:

* ``Generator\nat()`` which produces an integer >= 0.
* ``Generator\pos()`` which produces an integer > 0.
* ``Generator\neg()`` which produces an integer < 0.
* ``Generator\byte()`` which produces an integer >= 0 and <= 255.

.. literalinclude:: ../../examples/IntegerTest.php
   :language: php

For more precise and custom ranges, the ``Generator\choose()`` accepts a lower and upper bound for the interval to sample integers from.

.. literalinclude:: ../../examples/ChooseTest.php
   :language: php

.. _float:

Floats
------

``Generator\float()`` will produce a float value, which can be positive or negative. In this example, ``testAPropertyHoldingOnlyForPositiveNumbers`` fails very quickly.

.. literalinclude:: ../../examples/FloatTest.php
   :language: php

.. _bool:

Booleans
--------

``Generator\bool()`` produces a boolean, chosen between ``true`` and ``false``. It is mostly useful in conjunction with other Generators.

.. literalinclude:: ../../examples/BooleanTest.php
   :language: php

.. _string:

Strings
-------

``Generator\string()`` produces a string of arbitrary length. Only printable characters can be included in the string, which is UTF-8. Currently only ASCII characters between ``0x33`` and ``0x126`` are used.

.. literalinclude:: ../../examples/StringTest.php
   :language: php

.. seealso::

    For more complex use cases, try using a collection generator in conjunction with :ref:`char()<char>`. 

.. _char:

Characters
----------

``Generator\char()`` generates a character from the chosen charset, by default with a ``utf-8`` encoding. The only supported charset at the time of this writing is ``basic-latin``.

.. literalinclude:: ../../examples/CharacterTest.php
   :language: php

``Generator\charPrintableAscii()`` can also be used to limit the range of the character to the set of printable characters, from ``0x32`` to ``0x76``.

.. _constant:

Constants
---------

``Generator\constant()`` produces always the same value, which is the value used to initialize it. This Generator is useful for debugging and simplifying composite Generators in these occasions.

Often, as shown in ``testUseConstantGeneratorImplicitly``, constant are automatically boxed in this Generator if used where a ``Generator`` instance would be required:

.. literalinclude:: ../../examples/ConstantTest.php
   :language: php

.. _elements:

Elements
--------

``Generator\elements()`` produces a value randomly extracted from the specified array. Values can be specified as arguments or with a single, numeric array.

.. literalinclude:: ../../examples/ElementsTest.php
   :language: php

``testVectorOfElementsGenerators`` shows how to compose the Elements Generator into a :ref:`vector()<vector>` to build a vector of selected, sometimes repeated, elements.

.. seealso::

    :ref:`oneOf()<oneof>` does the same with values instead of Generators.
