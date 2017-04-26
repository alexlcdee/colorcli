<?php

namespace ColorCLI;

use MyCLabs\Enum\Enum;

/**
 * Enum BackgroundColors
 * @package ColorCLI
 *
 * @method static BackgroundColors BLACK()
 * @method static BackgroundColors RED()
 * @method static BackgroundColors GREEN()
 * @method static BackgroundColors YELLOW()
 * @method static BackgroundColors BLUE()
 * @method static BackgroundColors MAGENTA()
 * @method static BackgroundColors CYAN()
 * @method static BackgroundColors LIGHT_GRAY()
 */
class BackgroundColors extends Enum
{
    const BLACK = '40';
    const RED = '41';
    const GREEN = '42';
    const YELLOW = '43';
    const BLUE = '44';
    const MAGENTA = '45';
    const CYAN = '46';
    const LIGHT_GRAY = '47';
}