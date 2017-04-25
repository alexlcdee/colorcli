# Color CLI

PHP library for rendering CLI log messages with colored level labels. 
Implements PSR-3 LoggerInterface.

## Usage
#### Installation
```
composer require alexlcdee/colorcli
```
#### Usage
```php
require_once dirname(__DIR__) . '/vendor/autoload.php';

$logger = new \ColorCLI\Logger();

$logger->emergency('System is unusable.');
$logger->alert('Action must be taken immediately.');
$logger->critical('Critical conditions.');
$logger->error('Runtime errors that do not require immediate action but should typically');
$logger->warning('Exceptional occurrences that are not errors.');
$logger->notice('Normal but significant events.');
$logger->info('Interesting events.');
$logger->debug('Detailed debug information.');

```