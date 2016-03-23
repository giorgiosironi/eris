.. _randomness:

Randomness
==========

Eris allow multiple sources of randomness, with the requirements that they must accept a seed for reproducibility. Therefore, sequence Pseudo Random Number Generators (PRNG) are used instead of Cryptographically Secure PRNG, which would provide no additional value in generating test cases but make impossible to run the same test twice.

The supported random number generators are:

* the ``rand`` PHP function: this is the default, and simpler, choice.
* the ``mt_rand`` PHP function: this is a faster PRNG.
* the PHP code implementation ``purePhpMtRand()`` is equivalent to ``mt_rand``.
  
Being implemented inside a PHP object, ``purePhpMtRand()`` allows to isolate its state while the first two implementations modify the global state of the PHP process. Use ``purePhpMtRand()`` when your code calls ``rand()`` or ``mt_rand()`` and you don't want it to interact with the testing framework.

Configuration
-------------

.. literalinclude:: ../examples/RandConfigurationTest.php
   :language: php

``testUsingTheDefaultRandFunction`` specifies the ``rand`` variant, but is equivalent to not calling ``withRand()`` at all. ``srand`` is the corresponding seed function.

``testUsingTheDefaultMtRandFunction`` configured ``mt_rand`` and ``mt_srand`` as its seed function.

``testUsingThePurePhpMtRandFunction`` configures ``purePhpMtRand()``.

.. _randomness-size:

Maximum sizes
-------------

The size that can be set and actually reached with ``withMaxSize()`` is limited by the chosen PRNG.

* For ``rand`` the maximum values is the result of ``getrandmax()``, which is platform dependent but usually 2^31-1.
* For ``mt_rand`` the maximum value is the result of ``mt_getrandmax()``, which is also platform dependent but usually 2^31-1.
* For ``purePhpMtRand()``, being implemented in PHP code, the maximum value is 2^32-1.

The limitation on size depends not only on the processor architecture but also on the parameters of the algorithm. Both ``mt_rand`` and `purePhpMtRand()`` implement `MT19937`_, which generates 32-bit integers that can be scaled on any smaller interval.

However, according to the `PHP source code`_, ``mt_rand`` implementations uses a lower limit for backward compatibility with ``rand``. ``purePhpMtRand()`` has no need for backward compatibility and chooses to allow numbers up to 2^32-1, which is the maximum unsigned number representable with 32 bit.

.. _MT19937: https://en.wikipedia.org/wiki/Mersenne_Twister 
.. _PHP source code: https://github.com/php/php-src/blob/master/ext/standard/rand.c#L361

Seeding
-------

The PRNGS are seeded using the ``microtime()`` of the system, or with the ``ERIS_SEED`` environment variable for :ref:`test reproducibility<reproducibility>`.

Comparison
----------

==================== ================ ====== ============
Variant              Portability      Speed  Global state
==================== ================ ====== ============
``rand``             PHP core         Slow   Yes
``mt_rand``          PHP core         Fast   Yes
``purePhpMtRand()``  Eris source code Medium No
==================== ================ ====== ============

