Composite generators
=================

Composite Generators wire together existing Generators and callables to provide additional behavior.

Bind
----

Bind allows a Generator's output to be used as an input to create another Generator. This composition allows to create several random values which are correalted with each other, by using the same input for their Generators parameters.

For example, here's how to create a vector along with a random element chosen by it.

.. literalinclude:: ../../examples/BindTest.php
   :language: php
