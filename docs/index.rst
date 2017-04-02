Welcome to Eris's documentation!
================================

.. image:: eris.jpg
   :width: 25%
   :align: right
   :alt: the Eris dwarf planet

Eris is a porting of `QuickCheck`_  and property-based testing tools to the PHP and PHPUnit ecosystem.

.. _QuickCheck: https://hackage.haskell.org/package/QuickCheck

In property-based testing, several properties that the System Under Test must respect are defined, and a large sample of generated inputs is sent to it in an attempt to break the properties. With a few lines of code, hundreds of test cases can be generated and run.

    "Don't write tests. Generate them."
    -- John Hughes

Eris is the `Greek goddess`_ of chaos, strife, and discord. It tries to break your code with the most random and chaotic input and actions.

.. _Greek goddess: https://en.wikipedia.org/wiki/Eris_%28mythology%29

.. toctree::
   :maxdepth: 2

   installation
   getting_started
   shrinking
   generators/scalar
   generators/collections
   generators/composite
   generators/domain
   limits
   reproducibility
   listeners
   randomness
   outside_phpunit

.. filtering: when and suchthat?
.. random configurability: rand, mt_rand, else?
.. worked out examples: sort is the first
.. listener: minimum evaluation ratio
.. sample and sample shrinking

.. * :ref:`genindex`
.. * :ref:`modindex`
.. * :ref:`search`

