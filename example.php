<?php

use Psr\Log\LogLevel;
use TrashPanda\ProgressBarLog\ProgressBarLog;

require_once __DIR__ . '/vendor/autoload.php';

$progressLog = new ProgressBarLog(6, 10);
$progressLog->getProgressBar()->setBarCharacter('<fg=green>=</>');
$progressLog->getProgressBar()->setProgressCharacter('<fg=green>></>');
$progressLog->getProgressBar()->setBarWidth(100);
$progressLog->getProgressBar()->setMessage('Starting the example...', 'title');
$progressLog->getProgressBar()->setFormat("\n \033[44;37m %title:-37s% \033[0m\n\n %current%/%max% %bar% %percent:3s%%\n\n 🏁  %remaining% (<info>%memory%</info>)");
$progressLog->start();

sleep(1);

$progressLog->addLog(LogLevel::ERROR, 'S.A.I.N.T cannot locate the specified parts: "Johnny 5"');
sleep(1);
$progressLog->addLog(LogLevel::ERROR, 'Robot: "Johnny 5" is unaccounted for and missing');
sleep(1);
$progressLog->advance();
sleep(1);
$progressLog->addLog(LogLevel::ERROR, 'Robot: "Johnny 5" has gained enlightenment');
$progressLog->advance();
$progressLog->advance();
sleep(1);
$progressLog->addLog(LogLevel::NOTICE, 'Robot: "Johnny 5" has educated itself via books and TV.');
$progressLog->advance();
$progressLog->addLog(LogLevel::DEBUG, 'Robot: "Johnny 5" has been betrayed by it\'s carer.');
$progressLog->advance();
$progressLog->addLog(LogLevel::CRITICAL, 'Robot: "Johnny 5" evades capture and escapes again.');
$progressLog->addLog(LogLevel::EMERGENCY, 'Military attack launched against "Johnny 5".');
$progressLog->advance();
$progressLog->advance();
sleep(1);
$progressLog->addLog(LogLevel::WARNING, 'Robot: "Johnny 5" destroyed.');
sleep(1);
$progressLog->advance();
$progressLog->advance();
$progressLog->advance();

$progressLog->addLog('non-psr-log-level', 'JOHNNY 5 IS ALIVE!');
sleep(1);

