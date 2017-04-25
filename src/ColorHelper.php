<?php

namespace ColorCLI;

class ColorHelper
{
    public static function colorString($string, ForegroundColors $foregroundColor = null, BackgroundColors $backgroundColor = null)
    {
        $coloredString = '';
        if ($foregroundColor !== null) {
            $coloredString .= "\33[" . $foregroundColor . "m";
        }
        if ($backgroundColor !== null) {
            $coloredString .= "\33[" . $backgroundColor . "m";
        }
        $coloredString .= $string . "\033[0m";
        return $coloredString;
    }
}