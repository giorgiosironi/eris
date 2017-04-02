<?php
namespace Eris;

class Facade
{
    use TestTrait;

    public function __construct()
    {
        $this->erisSetupBeforeClass();
        $this->erisSetup();
    }
}
