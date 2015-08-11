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

namespace CamelSpider\Spider;

use CamelSpider\Entity\InterfaceLink;
use CamelSpider\Entity\InterfaceSubscription;

/**
 * Especializado em consultas aos documentos,
 * em seus diversos lugares.
 */
class DocumentManager
{
    /**
     * Verifica se o documento é novo.
     *
     * @todo implementar get Raw
     */
    public static function isFresh(
        $body, InterfaceLink $link,
        InterfaceSubscription $subscription
    ) {
        if ($existent = $subscription->getLink($link->getId())) {
            if (
                SpiderText::diffPercentage(
                    $existent,
                    $this->getBody()
                )
                < $this->getConfig('requirement_diff', 40)
            ) {
                return false;
            }
        }

        return true;
    }
}