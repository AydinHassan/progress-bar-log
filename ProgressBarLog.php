<?php

namespace TrashPanda\ProgressBarLog;

use Psr\Log\LogLevel;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ProgressBarLog
{
    /**
     * @var ConsoleOutput
     */
    private $output;

    /**
     * @var ProgressBar
     */
    private $progressBar;

    /**
     * @var int
     */
    private $numLogsToDisplay;

    /**
     * @var int
     */
    private $numRecords;

    /**
     * @var array
     */
    private $logs = [];

    /**
     * @var array
     */
    private static $severityColourMap = [
        LogLevel::EMERGENCY => 'red',
        LogLevel::ALERT => 'red',
        LogLevel::CRITICAL => 'red',
        LogLevel::ERROR => 'yellow',
        LogLevel::WARNING => 'yellow',
        LogLevel::NOTICE => 'cyan',
        LogLevel::INFO => 'cyan',
        LogLevel::DEBUG => 'default',
    ];

    /**
     * @var int
     */
    private static $severityPad = 9;

    public function __construct(int $numLogsToDisplay, int $numRecords = null)
    {
        $this->numLogsToDisplay = $numLogsToDisplay;
        $this->numRecords = $numRecords;
    }

    public function getOutput() : OutputInterface
    {
        if (!$this->output) {
            $this->output = new ConsoleOutput;
        }

        return $this->output;
    }

    /**
     * Allows to modify the progress bar instance
     *
     * @return ProgressBar
     */
    public function getProgressBar() : ProgressBar
    {
        if (!$this->progressBar) {
            $this->progressBar = new ProgressBar($this->getOutput(), $this->numRecords);
            $this->progressBar->setFormat('debug');
            $this->progressBar->setBarWidth((int) exec('tput cols'));
        }

        return $this->progressBar;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function start()
    {
        $this->clear();
        $this->getProgressBar()->start();
    }

    public function advance()
    {
        $this->clear();
        $this->getProgressBar()->advance();
        $this->drawLogs();
    }

    public function addLog(string $severity, string $line)
    {
        if (count($this->logs) === $this->numLogsToDisplay) {
            array_shift($this->logs);
        }

        $this->logs[] = ['severity' => $severity, 'line' => $line, 'time' => new \DateTime];

        $this->clear();
        $this->getProgressBar()->display();
        $this->drawLogs();
    }

    private function drawLogs()
    {
        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('');

        foreach ($this->logs as $log) {
            $this->getOutput()->writeln(sprintf(
               '  <comment>%s</comment> - <bg=%s> %s </> : %s',
               $log['time']->format('H:i:s'),
               static::$severityColourMap[$log['severity']] ?? 'green',
               str_pad(strtoupper($log['severity']), static::$severityPad, ' ', STR_PAD_LEFT),
               $log['line']
           ));
        }
    }

    private function clear()
    {
        $this->getOutput()->write("\033[2J");
        $this->moveCursorToTop();
    }

    /**
     * Move the cursor to the top left of the window
     *
     * @return void
     */
    private function moveCursorToTop()
    {
        $this->getOutput()->write("\033[H");
    }
}