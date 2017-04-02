<?php
namespace Eris\Shrinker;

class ShrinkerFactory
{
    private $options;
    
    /**
     * @param array $options
     *  'timeLimit' => null|integer  in seconds. The maximum time that should
     *                               be allocated to a Shrinker before giving up
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function multiple(array $generators, callable $assertion)
    {
        return $this->configureShrinker(new Multiple($generators, $assertion));
    }

    private function configureShrinker($shrinker)
    {
        if ($this->options['timeLimit'] !== null) {
            $shrinker->setTimeLimit(FixedTimeLimit::realTime($this->options['timeLimit']));
        }
        return $shrinker;
    }
}
