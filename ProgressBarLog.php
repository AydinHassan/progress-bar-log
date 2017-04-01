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
    private $progressBarHeight;

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
        $rp = new \ReflectionProperty(ProgressBar::class, 'internalFormat');
        $rp->setAccessible(true);
        $this->progressBarHeight = substr_count($rp->getValue($this->getProgressBar()), "\n");

        $this->clearScreen();
        $this->getProgressBar()->start();

        $this->getOutput()->writeln('');
        $this->getOutput()->writeln('');
    }

    public function advance()
    {
        $this->moveCursorToTop();
        $this->getProgressBar()->advance();
        $this->moveToLastLine();
    }

    public function addLog(string $severity, string $line)
    {
        $this->clearLogs();
        if (count($this->logs) === $this->numLogsToDisplay) {
            array_shift($this->logs);
        }

        $this->logs[] = ['severity' => $severity, 'line' => $line, 'time' => new \DateTime];
        $this->drawLogs();
    }

    private function drawLogs()
    {
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

    private function clearLogs()
    {
        // Erase previous lines
        if (count($this->logs) > 0) {
            $this->getOutput()->write("\x0D");
            $this->output->write(str_repeat("\x1B[1A\x1B[2K", count($this->logs)));
        }
    }

    private function clearScreen()
    {
        $this->getOutput()->write("\x1B[2J");
        $this->moveCursorToTop();
    }

    private function moveToLastLine()
    {
        $lastLine = $this->progressBarHeight + count($this->logs) + 3;
        $this->getOutput()->write(sprintf("\x1B[%d;0H", $lastLine));
    }

    /**
     * Move the cursor to the top left of the window
     *
     * @return void
     */
    private function moveCursorToTop()
    {
        $this->getOutput()->write("\x1B[H");
    }
}