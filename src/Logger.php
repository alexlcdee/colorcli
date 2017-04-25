<?php

namespace ColorfulLogger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Logger extends AbstractLogger
{
    /**
     * @var ForegroundColors[]
     */
    private static $foregroundColorMap = null;

    /**
     * @var BackgroundColors[]
     */
    private static $backgroundColorMap = null;

    /**
     * @var resource[]
     */
    private static $streamsMap = null;

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $prefix = ColorHelper::colorString(ucfirst("[{$level}]"), $this->getFGColor($level), $this->getBGColor($level));
        fputs($this->getStream($level), "{$prefix}: $message\n");
    }

    /**
     * @param $level
     * @return ForegroundColors|null
     */
    private function getFGColor($level)
    {
        if (static::$foregroundColorMap === null) {
            static::$foregroundColorMap = [
                LogLevel::EMERGENCY => new ForegroundColors(ForegroundColors::YELLOW),
                LogLevel::ALERT => new ForegroundColors(ForegroundColors::WHITE),
                LogLevel::CRITICAL => new ForegroundColors(ForegroundColors::RED),
                LogLevel::ERROR => new ForegroundColors(ForegroundColors::LIGHT_RED),
                LogLevel::WARNING => new ForegroundColors(ForegroundColors::YELLOW),
                LogLevel::NOTICE => new ForegroundColors(ForegroundColors::LIGHT_BLUE),
                LogLevel::INFO => new ForegroundColors(ForegroundColors::LIGHT_GREEN),
                LogLevel::DEBUG => null
            ];
        }
        return static::$foregroundColorMap[$level] ?? null;
    }

    /**
     * @param $level
     * @return BackgroundColors|null
     */
    private function getBGColor($level)
    {
        if (static::$backgroundColorMap === null) {
            static::$backgroundColorMap = [
                LogLevel::EMERGENCY => new BackgroundColors(BackgroundColors::RED),
                LogLevel::ALERT => new BackgroundColors(BackgroundColors::RED),
                LogLevel::CRITICAL => new BackgroundColors(BackgroundColors::YELLOW),
                LogLevel::ERROR => null,
                LogLevel::WARNING => null,
                LogLevel::NOTICE => null,
                LogLevel::INFO => null,
                LogLevel::DEBUG => null
            ];
        }
        return static::$backgroundColorMap[$level] ?? null;
    }

    /**
     * @param $level
     * @return resource
     */
    private function getStream($level)
    {
        if (static::$streamsMap === null) {
            defined('STDOUT') || define('STDOUT', fopen('php://stdout', 'w'));
            defined('STDERR') || define('STDERR', fopen('php://stderr', 'w'));
            static::$streamsMap = [
                LogLevel::EMERGENCY => STDERR,
                LogLevel::ALERT => STDERR,
                LogLevel::CRITICAL => STDERR,
                LogLevel::ERROR => STDERR,
                LogLevel::WARNING => STDERR,
                LogLevel::NOTICE => STDOUT,
                LogLevel::INFO => STDOUT,
                LogLevel::DEBUG => STDOUT
            ];
        }
        return static::$streamsMap[$level] ?? STDERR;
    }
}