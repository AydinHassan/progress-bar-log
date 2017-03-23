<h1 align="center">Progress Bar Log</h1>

<p align="center">Display a progress bar and a portion of a log at the same time on the CLI!</p>

[![Preview](https://asciinema.org/a/dnyvr047wc0dfdrbalalau38n.png)](https://asciinema.org/a/dnyvr047wc0dfdrbalalau38n)

Click the image above to see it in action.

## Installation

```sh
$ composer require trash-panda/progress-bar-log
```

## Use cases

Originally I wanted this for long running import/export work. Being able to see a subset of the most recent log
entries and also still see the progress and memory usage without scrolling the terminal. I figured it might be useful for 
some other people too.

## Usage

```php
<?php

use Psr\Log\LogLevel;
use TrashPanda\ProgressBarLog\ProgressBarLog;

require_once __DIR__ . '/vendor/autoload.php';

//The first parameter is the number of log lines to be displayed. The newest entries will be displayed - like a tail.
//The second parameter is the maximum number of steps for the progress bar
$progressLog = new ProgressBarLog(6, 10);
$progressLog->start();

//advance the progress bar by one
$progressLog->advance();

//Add a log line - the first parameter is a psr/log severity constant
//you can pass whatever you want there - but if it is a psr/log constant then the severity is colored accordingly
$progressLog->addLog(LogLevel::CRITICAL, 'Some mission critical error');
```

See `example.php` for a working script:

```sh
git clone git@github.com:AydinHassan/progress-bar-log.git
cd progress-bar-log
php example.php
```

### Customising the progress bar

The underlying progress bar is an instance of `\Symfony\Component\Console\Helper\ProgressBar`.

You can modify the settings of the progress bar by getting access to the instance via `getProgressBar()`:

```php
<?php

use TrashPanda\ProgressBarLog\ProgressBarLog;

require_once __DIR__ . '/vendor/autoload.php';

$progressLog = new ProgressBarLog(6, 10);
$progressLog->getProgressBar()->setFormat('normal');
$progressLog->getProgressBar()->setBarWidth(50);
$progressLog->start();
```

## Running unit tests

```sh
$ composer test
```
