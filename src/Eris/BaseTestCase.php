<?php
namespace Eris;

abstract class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    use TestTrait;

    protected $iterations = 100;
}
