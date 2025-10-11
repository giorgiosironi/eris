<?php
namespace Eris;

final class PHPUnitCommand implements \Stringable
{
    private function __construct(private $seed, private $name)
    {
    }

    public static function fromSeedAndName($seed, $name): self
    {
        return new self(
            $seed,
            str_replace('\\', '\\\\', $name)
        );
    }

    public function __toString(): string
    {
        return "ERIS_SEED={$this->seed} vendor/bin/phpunit --filter '{$this->name}'";
    }
}
