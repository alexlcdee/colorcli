<?php

namespace ColorCLI;

abstract class Enum
{
    const __default = null;

    private static $constCacheArray = NULL;

    protected $currentValue;

    final public function __construct($value, $useStrictMode = true)
    {
        $className = get_class($this);
        if (!self::isValidValue($value, $useStrictMode)) {
            throw new InvalidValueException(strtr("Value {value} not found in enum {class}", [
                '{class}' => $className,
                '{value}' => $value
            ]));
        }
        $this->currentValue = $value;
    }

    final public function isValidValue($value, $useStrictMode = true)
    {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $useStrictMode);
    }

    final protected static function getConstants()
    {
        if (self::$constCacheArray === NULL) {
            self::$constCacheArray = [];
        }
        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new \ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }

    final public function __toString()
    {
        return $this->currentValue;
    }
}