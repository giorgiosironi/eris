Welcome to Eris's documentation!
================================

Eris is a porting of `QuickCheck`_  and property-based testing tools to the PHP and PHPUnit ecosystem.

.. _QuickCheck: https://hackage.haskell.org/package/QuickCheck

In property-based testing, several properties that the System Under Test must respect are defined, and a large sample of generated inputs is sent to it in an attempt to break the properties.

.. toctree::
   :maxdepth: 2

   installation
   getting_started
   shrinking
   generators/scalar
   generators/collections
   generators/composite
   limits
.. generators/domainbased names,date,regex
.. filtering: when and cross reference such that
.. time limits, assertions, error management that bubble ups
.. listeners, collect values, log files as documentations
.. constant should go into generators/scalar
.. elements generator, where?
.. oneof, frequency, cross reference with elements
.. check from e to z in examples
.. sample and sample shrinking
.. worked out examples: sort is the first
.. reproducibility: seed
.. some words on exported functionality with functions
.. minimum evaluation ratio

.. * :ref:`genindex`
.. * :ref:`modindex`
.. * :ref:`search`

