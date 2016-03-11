Welcome to Eris's documentation!
================================

Eris is a porting of `QuickCheck`_  and property-based testing tools to the PHP and PHPUnit ecosystem.

.. _QuickCheck: https://hackage.haskell.org/package/QuickCheck

In property-based testing, several properties that the System Under Test must respect are defined, and a large sample of generated inputs is sent to it in an attempt to break the properties.

.. toctree::
   :maxdepth: 2

   installation
   getting_started
   generators/scalar
   generators/collections
   generators/composite
   limits
.. generators/transformations map, bind
.. generators/domainbased names,date,regex
.. filtering: suchthat vs when
.. shrinking, time limits, assertions, error management that bubble ups
.. listeners, collect values

.. * :ref:`genindex`
.. * :ref:`modindex`
.. * :ref:`search`

