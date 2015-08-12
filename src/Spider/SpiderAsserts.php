<?php

/*
 * This file is part of gpupo/camel-webspider
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For more information, see
 * <http://www.g1mr.com/camel-webspider/>.
 */

namespace Gpupo\CamelWebspider\Spider;

use Gpupo\CamelWebspider\Entity\InterfaceLink;
use Zend\Uri\Uri;

class SpiderAsserts
{
    public static function containKeywords($txt, $keywords = null, $ifNull = true)
    {
        if (!is_array($keywords) || count($keywords) < 1) {
            return $ifNull; // Subscription not contain filter for keywords
        }
        foreach ($keywords as $keyword) {
            if (mb_strpos(strtolower($txt), strtolower($keyword)) !== false) {
                return true;
            }
        }

        return false;
    }

    public static function isDocumentHref($href)
    {
        if (
            empty($href)                            ||
            stripos($href, 'mail') !== false        ||
            stripos($href, '.pdf') !== false        ||
            stripos($href, '.ppt') !== false        ||
            substr($href, 0, 10) === 'javascript'     ||
            substr($href, 0, 1) === '#'
        ) {
            return false;
        }
        foreach (['%20', '='] as $c) {
            if (stripos($href, $c.'http://') !== false) {
                return false;
            }
        }

        $zendUri = new Uri($href);
        if ($zendUri->isValid()) {
            return true;
        }

        return false;
    }

    public static function isDocumentLink(InterfaceLink $link)
    {
        return self::isDocumentHref($link->getHref());
    }
}
