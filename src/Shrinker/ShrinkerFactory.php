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

    public function random(array $generators, callable $assertion)
    {
        $shrinker = new Random($generators, $assertion);    
        if ($this->options['timeLimit'] !== null) {
            $shrinker->setTimeLimit(FixedTimeLimit::realTime($this->options['timeLimit']));
        }
        return $shrinker;
    }
}
