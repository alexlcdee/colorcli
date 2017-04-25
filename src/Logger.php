<?php

namespace ColorCLI;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Logger extends AbstractLogger
{
    /**
     * @var ForegroundColors[]
     */
    protected static $foregroundColorMap = null;

    /**
     * @var BackgroundColors[]
     */
    protected static $backgroundColorMap = null;

    /**
     * @var resource[]
     */
    protected static $streamsMap = null;

    /**
     * @var string[]
     */
    protected static $levels = null;

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     * @throws InvalidValueException
     */
    public function log($level, $message, array $context = array())
    {
        $this->checkLevel($level);
        $prefix = ColorHelper::colorString(strtoupper("[{$level}]"), $this->getFGColor($level), $this->getBGColor($level));
        fputs($this->getOutputStream($level), "{$prefix}: $message\n");
    }

    /**
     * Check if level exists in list of possible levels
     * @param mixed $level
     */
    public function checkLevel($level)
    {
        if (static::$levels === null) {
            $this->loadLevels();
        }

        if (!in_array($level, static::$levels)) {
            $levels = implode(', ', static::$levels);
            throw new \UnexpectedValueException("Level must be one of [$levels].");
        }
    }

    private function loadLevels()
    {
        $reflection = new \ReflectionClass(LogLevel::class);
        static::$levels = $reflection->getConstants();
    }

    /**
     * Get foreground color for specified level
     * @param mixed $level
     * @return ForegroundColors|null
     */
    public function getFGColor($level)
    {
        if (static::$foregroundColorMap === null) {
            $this->resetFGColors();
        }
        return static::$foregroundColorMap[$level] ?? null;
    }

    /**
     * Set foreground colors map to default ones
     */
    public function resetFGColors()
    {
        static::$foregroundColorMap = [
            LogLevel::EMERGENCY => ForegroundColors::YELLOW(),
            LogLevel::ALERT => ForegroundColors::WHITE(),
            LogLevel::CRITICAL => ForegroundColors::RED(),
            LogLevel::ERROR => ForegroundColors::LIGHT_RED(),
            LogLevel::WARNING => ForegroundColors::YELLOW(),
            LogLevel::NOTICE => ForegroundColors::LIGHT_BLUE(),
            LogLevel::INFO => ForegroundColors::LIGHT_GREEN(),
            LogLevel::DEBUG => null
        ];
    }

    /**
     * Get background color for specified level
     * @param mixed $level
     * @return BackgroundColors|null
     */
    public function getBGColor($level)
    {
        if (static::$backgroundColorMap === null) {
            $this->resetBGColor();
        }
        return static::$backgroundColorMap[$level] ?? null;
    }

    /**
     * Set background colors map to default ones
     */
    public function resetBGColor()
    {
        static::$backgroundColorMap = [
            LogLevel::EMERGENCY => BackgroundColors::RED(),
            LogLevel::ALERT => BackgroundColors::RED(),
            LogLevel::CRITICAL => BackgroundColors::YELLOW(),
            LogLevel::ERROR => null,
            LogLevel::WARNING => null,
            LogLevel::NOTICE => null,
            LogLevel::INFO => null,
            LogLevel::DEBUG => null
        ];
    }

    /**
     * Get output stream for specified level
     * @param mixed $level
     * @return resource
     */
    public function getOutputStream($level)
    {
        if (static::$streamsMap === null) {
            $this->resetOutputStreams();
        }
        return static::$streamsMap[$level] ?? STDERR;
    }

    /**
     * Set output streams map to default map
     */
    public function resetOutputStreams()
    {
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

    /**
     * Set foreground color for specified level
     * @param mixed $level
     * @param ForegroundColors $color
     * @return $this
     */
    public function setFGColor($level, ForegroundColors $color)
    {
        if (static::$foregroundColorMap === null) {
            $this->resetFGColors();
        }
        $this->checkLevel($level);
        static::$foregroundColorMap[$level] = $color;
        return $this;
    }

    /**
     * Set background color for specified level
     * @param mixed $level
     * @param ForegroundColors $color
     * @return $this
     */
    public function setBGColor($level, ForegroundColors $color)
    {
        if (static::$foregroundColorMap === null) {
            $this->resetBGColor();
        }
        $this->checkLevel($level);
        static::$foregroundColorMap[$level] = $color;
        return $this;
    }

    /**
     * Set output stream for specified level
     * @param mixed $level
     * @param resource $stream
     * @return $this
     */
    public function setOutputStream($level, $stream)
    {
        if (static::$streamsMap === null) {
            $this->resetOutputStreams();
        }
        $this->checkLevel($level);
        if (!is_resource($stream)) {
            throw new \UnexpectedValueException("Argument '\$stream' must be a writable stream resource");
        }
        static::$streamsMap[$level] = $stream;
        return $this;
    }
}