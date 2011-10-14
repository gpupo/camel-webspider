<?php

namespace CamelSpider\Spider;

use CamelSpider\Entity\InterfaceSubscription,
    CamelSpider\Entity\InterfaceLink;


/**
 * Especializado em consultas aos documentos,
 * em seus diversos lugares
 */

class DocumentManager
{
    /**
     * Verifica se o documento é novo
     * @todo implementar get Raw
     */
    public static function isFresh($body, InterfaceLink $link, InterfaceSubscription $subscription)
    {
        if($existent = $subscription->getLink($link->getId()))
        {
            if(
                SpiderText::diffPercentage(
                    $existent,
                    $this->getBody()
                )
                < $this->getConfig('requirement_diff', 40)
            ){
                return false;
            }
        }
        return true;
    }

}
