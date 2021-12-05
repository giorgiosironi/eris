<?php
namespace Eris\Antecedent;

use Eris\Antecedent;
use Eris\Antecedents;

/**
 * @see Antecedents::printableCharacter()
 */
function printableCharacter()
{
    return Antecedents::printableCharacter();
}

/**
 * @see Antecedents::printableCharacters()
 */
function printableCharacters()
{
    return Antecedents::printableCharacters();
}

class PrintableCharacter implements Antecedent
{
    /**
     * Assumes utf-8.
     */
    public function evaluate(array $values)
    {
        foreach ($values as $char) {
            if (ord($char) < 32) {
                return false;
            }
            if (ord($char) === 127) {
                return false;
            }
        }
        return true;
    }
}
