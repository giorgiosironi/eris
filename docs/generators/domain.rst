Domain-based generators
=======================

Some default Generators target a particular business domain. They can be useful to test applications with plausible data instead of with universal values coming from a mathematical set like natural numbers or all the possible strings.

Names
-----

Person names can be generated by selecting random elements from a dataset stored inside Eris source code.

.. literalinclude:: ../../examples/NamesTest.php
   :language: php

``testGeneratingNames`` shows a list of sample generated names. Their length increase with the size passed to Generators:

.. code-block:: bash

    string(0) ""
    string(0) ""
    string(3) "Ita"
    string(6) "Teresa"
    string(8) "Raimunde"
    string(7) "Laelius"
    string(5) "Fanny"
    string(6) "Aileen"
    string(11) "Marie-Elise"
    string(7) "Ignacio"
    string(8) "Hendrick"

``testSamplingShrinkingOfNames`` shows how names are shrinked to the slightly shorter name in the data set that is more similar to the current value:

.. code-block:: bash

    array(8) {
      [0]=>
      string(9) "Gwenaelle"
      [1]=>
      string(8) "Ganaelle"
      [2]=>
      string(7) "Anaelle"
      [3]=>
      string(6) "Abelle"
      [4]=>
      string(5) "Abele"
      [5]=>
      string(4) "Abel"
      [6]=>
      string(3) "Abe"
      [7]=>
      string(2) "Di"
    }


Dates
-----

The ``date()`` Generator produces uniformly distributed DateTime objects.

.. literalinclude:: ../../examples/DateTest.php
   :language: php

``testYearOfADate`` shows how to specify the lower and upper bound of an interval to pick dates from. These bounds are included in the interval.

``testDefaultValuesForTheInterval`` shows that by default, given the 32-bit random generators used as a source, dates span the 1970-2038 interval of 32-bit UNIX timestamps.

``testFromDayOfYearFactoryMethodRespectsDistanceBetweenDays`` uses the :ref:``choose()<choose>`` Generator to pick directly integers and build ``DateTime`` objects itself. The test demonstrates `a bug`_ in the ``datetime`` PHP extension when an off-by-one error can be introduced when dealing with leap years.

.. _a bug: https://bugs.php.net/bug.php?id=70956

Regex
-----

The ``regex()`` Generator attempts to build a string matching the specified regular expression. It can be used to produce input strings much more close to a plausible format than totally random values.

.. literalinclude:: ../../examples/RegexTest.php
   :language: php

Here is a sample of generated values from ``testStringsMatchingAParticularRegex``:

.. code-block:: bash

    string(10) "ylunkcebou"
    string(10) "whkjewwhud"
    string(10) "pwirjzhbdw"
    string(10) "dxsdwnsmyi"
    string(10) "ttgczpimxs"
    string(10) "jdsmlexlau"

