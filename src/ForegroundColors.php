<?php

namespace ColorCLI;

use MyCLabs\Enum\Enum;

/**
 * Class ForegroundColors
 * @package ColorCLI
 *
 * @method static ForegroundColors BLACK()
 * @method static ForegroundColors DARK_GRAY()
 * @method static ForegroundColors RED()
 * @method static ForegroundColors LIGHT_RED()
 * @method static ForegroundColors GREEN()
 * @method static ForegroundColors LIGHT_GREEN()
 * @method static ForegroundColors BROWN()
 * @method static ForegroundColors YELLOW()
 * @method static ForegroundColors BLUE()
 * @method static ForegroundColors LIGHT_BLUE()
 * @method static ForegroundColors PURPLE()
 * @method static ForegroundColors LIGHT_PURPLE()
 * @method static ForegroundColors CYAN()
 * @method static ForegroundColors LIGHT_CYAN()
 * @method static ForegroundColors LIGHT_GRAY()
 * @method static ForegroundColors WHITE()
 */
class ForegroundColors extends Enum
{
    const BLACK = '0;30';
    const DARK_GRAY = '1;30';
    const RED = '0;31';
    const LIGHT_RED = '1;31';
    const GREEN = '0;32';
    const LIGHT_GREEN = '1;32';
    const BROWN = '0;33';
    const YELLOW = '1;33';
    const BLUE = '0;34';
    const LIGHT_BLUE = '1;34';
    const PURPLE = '0;35';
    const LIGHT_PURPLE = '1;35';
    const CYAN = '0;36';
    const LIGHT_CYAN = '1;36';
    const LIGHT_GRAY = '0;37';
    const WHITE = '1;37';
}