<?php

namespace Eris;

use Eris\Antecedent\PrintableCharacter;

final class Antecedents
{
    public static function printableCharacter()
    {
        return new PrintableCharacter();
    }

    public static function printableCharacters()
    {
        return new PrintableCharacter();
    }
}
