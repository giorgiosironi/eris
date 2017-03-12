<?php
namespace Eris;

final class PHPUnitCommand
{
    private $seed;
    private $name;

    private function __construct($seed, $name)
    {
        $this->seed = $seed;
        $this->name = $name;
    }

    public static function fromSeedAndName($seed, $name)
    {
        return new self(
            $seed,
            str_replace('\\', '\\\\', $name)
        );
    }

    public function __toString()
    {
        return "ERIS_SEED={$this->seed} vendor/bin/phpunit --filter '{$this->name}'";
    }
}
