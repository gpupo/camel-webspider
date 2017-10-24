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

use Gpupo\CamelWebspider\Entity\InterfaceLink;
use Gpupo\CamelWebspider\Entity\InterfaceSubscription;

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
