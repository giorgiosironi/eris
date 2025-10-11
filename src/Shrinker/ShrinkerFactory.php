<?php
namespace Eris\Shrinker;

class ShrinkerFactory
{
    /**
     * @param array $options
     *  'timeLimit' => null|integer  in seconds. The maximum time that should
     *                               be allocated to a Shrinker before giving up
     */
    public function __construct(private array $options)
    {
    }

    public function multiple(array $generators, callable $assertion)
    {
        return $this->configureShrinker(new Multiple($generators, $assertion));
    }

    private function configureShrinker(\Eris\Shrinker\Multiple $shrinker): \Eris\Shrinker\Multiple
    {
        if ($this->options['timeLimit'] !== null) {
            $shrinker->setTimeLimit(FixedTimeLimit::realTime($this->options['timeLimit']));
        }
        return $shrinker;
    }
}
