<?php

declare(strict_types=1);

namespace Infection\Process\Listener;

use Infection\Config\InfectionConfig;
use Infection\EventDispatcher\EventSubscriberInterface;
use Infection\Events\MutationTestingFinished;
use Infection\Mutant\MetricsCalculator;

class TextFileLoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @var InfectionConfig
     */
    private $infectionConfig;

    /**
     * @var MetricsCalculator
     */
    private $metricsCalculator;

    public function __construct(InfectionConfig $infectionConfig, MetricsCalculator $metricsCalculator)
    {
        $this->infectionConfig = $infectionConfig;
        $this->metricsCalculator = $metricsCalculator;
    }

    public function getSubscribedEvents()
    {
        return [
            MutationTestingFinished::class => [$this, 'onMutationTestingFinished'],
        ];
    }

    public function onMutationTestingFinished(MutationTestingFinished $event)
    {
        $textFileLogPath = $this->infectionConfig->getTextFileLogPath();

        if ($textFileLogPath) {
            $logParts = [];

            $processes = $this->metricsCalculator->getEscapedMutantProcesses();
            $logParts[] = 'Escaped mutants:';

            foreach ($processes as $index => $mutantProcess) {
                $logParts[] = sprintf('%d) %s', $index + 1, get_class($mutantProcess->getMutant()->getMutation()->getMutator()));
                $logParts[] = $mutantProcess->getMutant()->getMutation()->getOriginalFilePath();
                $logParts[] = $mutantProcess->getMutant()->getDiff();
                $logParts[] = $mutantProcess->getProcess()->getOutput();
            }

            file_put_contents($textFileLogPath, implode($logParts, "\n"));
        }
    }
}