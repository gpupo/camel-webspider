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

use Gpupo\CamelWebspider\Entity\AbstractSpiderEgg;
use Gpupo\CamelWebspider\Entity\FactorySubscription;
use Gpupo\CamelWebspider\Entity\Pool;

/**
 * Aciona indexação para cada assinatura.
 */
class AbstractLauncher extends AbstractSpiderEgg
{
    protected $name = 'launcher';

    protected $indexer;

    public function __construct(Indexer $indexer, $logger)
    {
        $this->indexer = $indexer;
        $this->logger = $logger;
    }

    protected function getSampleSubscriptions()
    {
        return FactorySubscription::buildCollectionFromDomain(
            [
                'noticias.terra.com.br',
                'www.uol.com.br',
            ]
        );
    }

    /**
     * This method should be overloaded in your Launcher.
     */
    protected function doSave(Pool $pool)
    {
        foreach ($pool->toArray() as $link) {
            if (
                $link instanceof InterfaceLink &&
                $link->getRelevancy() >= $this->getConfig('minimal_relevancy', 3)
            ) {
                //do something with your DB!
            }
        }
    }

    public function run($subscriptionCollection = null)
    {
        if (!$subscriptionCollection) {
            //Tests only.
            $this->logger('Using subscriptions samples', 'err');
            $subscriptionCollection = $this->getSampleSubscriptions();
        }

        foreach ($subscriptionCollection as $subscription) {
            $this->logger(
                'Checking updates fo the subscription '
                .$subscription->getDomainString()
            );
            try {
                $pool = $this->indexer->run($subscription);
                $this->doSave($pool);
            } catch (\Exception $e) {
                echo "\nError: ".$e->getMessage();
                $this->logger($e->getMessage(), 'err');
            }
        }
    }
}
