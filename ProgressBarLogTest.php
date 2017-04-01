<?php

namespace TrashPanda\ProgressBarLog;

use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @author Aydin Hassan <aydin@hotmail.co.uk>
 */
class ProgressBarLogTest extends TestCase
{

    public function testOutput()
    {
        $progressBarLog = new ProgressBarLog(5, 5);
        $progressBarLog->setOutput($output = new BufferedOutput);
        $progressBarLog->getProgressBar()->setFormat('normal');
        $progressBarLog->getProgressBar()->setBarWidth(5);
        $progressBarLog->start();

        self::assertRegExp(
            $this->getExpectedRegex(['0\/5 \[>----\]   0%']),
            $output->fetch()
        );

        $progressBarLog->addLog(LogLevel::WARNING, 'Message 1');

        self::assertRegExp(
            $this->getExpectedRegex([
                "  \d{2}:\d{2}:\d{2} -    WARNING  : Message 1"
            ]),
            $output->fetch()
        );

        $progressBarLog->advance();

        self::assertRegExp(
            $this->getExpectedRegex([
                "1\/5 \[=>---\]  20%",
            ]),
            $output->fetch()
        );


        $progressBarLog->addLog(LogLevel::ERROR, 'Message 2');

        self::assertRegExp(
            $this->getExpectedRegex([
                "  \d{2}:\d{2}:\d{2} -    WARNING  : Message 1\n",
                "  \d{2}:\d{2}:\d{2} -      ERROR  : Message 2\n",
            ]),
            $output->fetch()
        );

        $progressBarLog->advance();

        self::assertRegExp(
            $this->getExpectedRegex([
                "2\/5 \[==>--\]  40%",
            ]),
            $output->fetch()
        );

        $progressBarLog->addLog(LogLevel::WARNING, 'Message 3');

        self::assertRegExp(
            $this->getExpectedRegex([
                "  \d{2}:\d{2}:\d{2} -    WARNING  : Message 1\n",
                "  \d{2}:\d{2}:\d{2} -      ERROR  : Message 2\n",
                "  \d{2}:\d{2}:\d{2} -    WARNING  : Message 3\n",
            ]),
            $output->fetch()
        );

        $progressBarLog->addLog(LogLevel::EMERGENCY, 'Message 4');

        self::assertRegExp(
            $this->getExpectedRegex([
                "  \d{2}:\d{2}:\d{2} -    WARNING  : Message 1\n",
                "  \d{2}:\d{2}:\d{2} -      ERROR  : Message 2\n",
                "  \d{2}:\d{2}:\d{2} -    WARNING  : Message 3\n",
                "  \d{2}:\d{2}:\d{2} -  EMERGENCY  : Message 4\n",
            ]),
            $output->fetch()
        );

        $progressBarLog->advance();

        self::assertRegExp(
            $this->getExpectedRegex([
                "3\/5 \[===>-\]  60%",
            ]),
            $output->fetch()
        );

        $progressBarLog->addLog(LogLevel::NOTICE, 'Message 5');

        self::assertRegExp(
            $this->getExpectedRegex([
                "  \d{2}:\d{2}:\d{2} -    WARNING  : Message 1\n",
                "  \d{2}:\d{2}:\d{2} -      ERROR  : Message 2\n",
                "  \d{2}:\d{2}:\d{2} -    WARNING  : Message 3\n",
                "  \d{2}:\d{2}:\d{2} -  EMERGENCY  : Message 4\n",
                "  \d{2}:\d{2}:\d{2} -     NOTICE  : Message 5\n",
            ]),
            $output->fetch()
        );

        $progressBarLog->addLog(LogLevel::DEBUG, 'Message 6');

        self::assertRegExp(
            $this->getExpectedRegex([
                "  \d{2}:\d{2}:\d{2} -      ERROR  : Message 2\n",
                "  \d{2}:\d{2}:\d{2} -    WARNING  : Message 3\n",
                "  \d{2}:\d{2}:\d{2} -  EMERGENCY  : Message 4\n",
                "  \d{2}:\d{2}:\d{2} -     NOTICE  : Message 5\n",
                "  \d{2}:\d{2}:\d{2} -      DEBUG  : Message 6\n",
            ]),
            $output->fetch()
        );

        $progressBarLog->advance();

        self::assertRegExp(
            $this->getExpectedRegex([
                "4\/5 \[====>\]  80%",
            ]),
            $output->fetch()
        );

        $progressBarLog->addLog(LogLevel::CRITICAL, 'Message 7');

        self::assertRegExp(
            $this->getExpectedRegex([
                "  \d{2}:\d{2}:\d{2} -    WARNING  : Message 3\n",
                "  \d{2}:\d{2}:\d{2} -  EMERGENCY  : Message 4\n",
                "  \d{2}:\d{2}:\d{2} -     NOTICE  : Message 5\n",
                "  \d{2}:\d{2}:\d{2} -      DEBUG  : Message 6\n",
                "  \d{2}:\d{2}:\d{2} -   CRITICAL  : Message 7\n",
            ]),
            $output->fetch()
        );

        $progressBarLog->advance();

        self::assertRegExp(
            $this->getExpectedRegex([
                "5\/5 \[=====\] 100%",
            ]),
            $output->fetch()
        );

        $progressBarLog->addLog(LogLevel::INFO, 'Message 8');

        self::assertRegExp(
            $this->getExpectedRegex([
                "  \d{2}:\d{2}:\d{2} -  EMERGENCY  : Message 4\n",
                "  \d{2}:\d{2}:\d{2} -     NOTICE  : Message 5\n",
                "  \d{2}:\d{2}:\d{2} -      DEBUG  : Message 6\n",
                "  \d{2}:\d{2}:\d{2} -   CRITICAL  : Message 7\n",
                "  \d{2}:\d{2}:\d{2} -       INFO  : Message 8\n",
            ]),
            $output->fetch()
        );

    }


    private function getExpectedRegex(array $output)
    {
        return '/' . implode('', $output) . '/';
    }
}
