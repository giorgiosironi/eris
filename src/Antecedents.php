<?php

namespace Eris;

use Eris\Antecedent\PrintableCharacter;

final class Antecedents
{
    public static function printableCharacter(): \Eris\Antecedent\PrintableCharacter
    {
        return new PrintableCharacter();
    }

    public static function printableCharacters(): \Eris\Antecedent\PrintableCharacter
    {
        return new PrintableCharacter();
    }
}
