<?php

/*
 * This file is part of gpupo/camel-webspider
 *
 * (c) Gilmar Pupo <contact@gpupo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For more information, see
 * <https://opensource.gpupo.com/camel-webspider/>.
 */

namespace Gpupo\CamelWebspider\Spider;

use Gpupo\CamelWebspider\Tools\IdeiasLang;

/**
 * Helper for Strings.
 */
class SpiderTxt
{
    public static function diffPercentage($a, $b)
    {
        $percentage = IdeiasLang::iDiff($a, $b);
        var_dump($percentage);

        return $percentage;
    }
}
