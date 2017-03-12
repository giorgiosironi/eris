<?php
use Eris\Generator;
use Eris\TestTrait;

class GeneratorSamplesTest extends PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function testGenerators()
    {
        $generators = [
            //"Gen\\int" => Generator\int(),
            "Gen\\neg" => Generator\neg(),
            //"Gen\\nat" => Generator\nat(),
            "Gen\\pos" => Generator\pos(),
            /*
            "Gen\\float" => Generator\float(),
            "Gen\\choose(30, 9000) - no size used" => Generator\choose(30, 9000),
            "Gen\\tuple(gen\\int, gen\\neg, gen\\string)" => Generator\tuple(Generator\int(), Generator\neg(), Generator\string()),
            "Gen\\list(gen\\string())" => Generator\seq(Generator\string()),
            "Gen\\vector(12, gen\\neg())" => Generator\vector(12, Generator\neg()),
            "Gen\\elements(10, 'hello-world', [1, 2])" => Generator\elements(10, 'hello-world', [1, 2]),
            "Gen\\oneOf(gen\\pos, gen\\neg, gen\\float])" => Generator\oneOf([Generator\pos(), Generator\neg(), Generator\float()),
            "Gen\\frequency([[3, gen\\pos()], [7, gen\\string()]])" => Generator\frequency([[1, Generator\pos()], [10, Generator\string()]]),
            */
        ];

        foreach ($generators as $description => $generator) {
            $this->generateSample($description, $generator);
        }
    }

    private function generateSample($description, $generator)
    {
        echo PHP_EOL;
        echo $description . " with size 10";
        $sample = $this->sample($generator);
        $this->assertInternalType('array', $sample->collected());
        $this->prettyPrint($sample->collected());
    }

    private function prettyPrint(array $samples)
    {
        echo PHP_EOL;
        foreach ($samples as $sample) {
            echo var_export($sample, true) . PHP_EOL;
        }
        echo PHP_EOL;
    }
}
