
Iteration is each one of the times generated values are created, but actual evaluations may be fewer as values are skipped by ``when()``. However, evaluation also happens during shrinking to check if the test still fails.
Ordinary evaluations are performed inside ``forAll()`` verifications, while additional evaluations may be performed during shrinking.
Each set of values used as input during an iteration is a generation.
