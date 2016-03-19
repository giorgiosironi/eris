Collection generators
=================

Collection-oriented Generators produce arrays conforming to different constraints depending on the mathematical definition the reproduce. All these Generators require as an input one or more Generators to be used to produce single elements.

.. _associative:

Associative arrays
------------------

Associative arrays can be generated composing other generators for each of the keys of the desired array, which will contain the specified fixed set of keys and vary the values.

.. literalinclude:: ../../examples/AssociativeArrayTest.php
   :language: php

.. _sequence:

Sequences
---------

Sequences are defined as numeric arrays with a variable amount of elements of a single type. Both the length of the array and its values will be randomly generated.

.. literalinclude:: ../../examples/SequenceTest.php
   :language: php

.. _vector:

Vectors
-------

Vectors are defined as numeric arrays with a fixed amount of elements of a single type. Only the values contained will be randomly generated.

As an example, consider vectors inside a fixed space such as the set of 2D or 3D points.

.. literalinclude:: ../../examples/VectorTest.php
   :language: php

.. _tuple:

Tuples
------

Tuples are defined as a small array of fixed size, consiting of a few heteregeneous types.

.. literalinclude:: ../../examples/TupleTest.php
   :language: php

.. _set:

Sets
----

Sets are defined as array with a variable amount of elements of a single type, without any repeated element.

.. literalinclude:: ../../examples/SetTest.php
   :language: php

.. _subset:

Subsets
----

Subsets are set whose elements are extracted from a fixed universe set, specified as an input.

.. literalinclude:: ../../examples/SubsetTest.php
   :language: php
