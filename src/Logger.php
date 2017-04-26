<?php

namespace ColorCLI;

use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
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

    protected $colorsEnabled = true;

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        $this->checkLevel($level);
        $prefix = $this->colorsEnabled ?
            ColorHelper::colorString(strtoupper("[{$level}]"), $this->getFGColor($level), $this->getBGColor($level)) :
            strtoupper("[{$level}]");
        $message = $this->interpolate($message, $context);
        if (isset($context['exception']) && $context['exception'] instanceof \Exception) {
            $message .= PHP_EOL . $this->handleException($context['exception']);
        }
        $message = $this->processMultiline($level, $message);
        fputs($this->getOutputStream($level), "{$prefix}: $message" . PHP_EOL);
    }

    /**
     * Check if level exists in list of possible levels
     * Psr\Log suggests to throw Psr\Log\InvalidArgumentException if  incompatible log level passed
     * @param mixed $level
     * @throws InvalidArgumentException
     */
    public function checkLevel($level)
    {
        if (static::$levels === null) {
            // Psr\Log does not provide Enum, so we need to load levels from LogLevel constants
            $reflection = new \ReflectionClass(LogLevel::class);
            static::$levels = $reflection->getConstants();
        }

        if (!in_array($level, static::$levels)) {
            $levels = implode(', ', static::$levels);
            throw new InvalidArgumentException("Level must be one of [$levels].");
        }
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
     * Replace placeholders in message with data from context
     * @param string $message
     * @param array $context
     * @return string
     * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md#12-message
     */
    protected function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * Handle Exception <br>
     * Render exception name, message and stack trace
     * @param \Exception $exception
     * @return string
     */
    protected function handleException(\Exception $exception)
    {
        return get_class($exception) . ": {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}"
            . PHP_EOL
            . "{$exception->getTraceAsString()}";
    }

    /**
     * Process multiline message <br>
     * Add left padding for every line of message
     * @param mixed $level
     * @param string $message
     * @return string
     */
    protected function processMultiline($level, $message)
    {
        $messageStringArray = preg_split('/\r?\n/', $message);
        $messageString = implode(PHP_EOL . $this->getPadding($level), $messageStringArray);
        return $messageString;
    }

    /**
     * @param mixed $level
     * @return string
     */
    protected function getPadding($level)
    {
        return str_pad('', strlen($level) + 4, ' ');
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

    /**
     * Disable colored output
     */
    public function disableColors()
    {
        $this->colorsEnabled = false;
    }

    /**
     * Enable colored output
     */
    public function enableColors()
    {
        $this->colorsEnabled = true;
    }
}