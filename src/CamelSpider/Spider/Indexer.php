<?php

/*
* This file is part of the CamelSpider package.
*
* (c) Gilmar Pupo <g@g1mr.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace CamelSpider\Spider;

use CamelSpider\Entity\Link,
    CamelSpider\Entity\InterfaceLink,
    CamelSpider\Entity\Document,
    CamelSpider\Entity\Pool,
    CamelSpider\Entity\InterfaceSubscription,
    CamelSpider\Spider\SpiderAsserts as a;

/**
 * Process every subscription
 *
 * @author      Gilmar Pupo <g@g1mr.com>
 *
*/
class Indexer extends AbstractSpider
{

    protected $name = 'Indexer';

    /**
    * @param \Goutte\Client Goutte $goutte Crawler Goutte
    * @param InterfaceCache $cache A class facade for Zend Cache
    * @param Monolog $logger Object to write logs (in realtime with low memory usage!)
    * @param array $config Overload of default configurations in the constructor
    **/
    public function __construct(\Goutte\Client $goutte, InterfaceCache $cache, $logger = NULL, array $config = NULL)
    {
        $this->setTime('total');
        $this->goutte = $goutte;
        $this->logger = $logger;
        $this->cache = $cache;
        parent::__construct(array(), $config);

        return $this;
    }

    protected function addLink(Link $link)
    {
        if (!$this->subscription->insideScope($link)) {

            $this->logger(
                'outside the scope'
                . "\n"
                . '['
                . $this->subscription->getDomainString()
                . "]\n[" 
                . $link->get('href') 
                . ']'
            ,'info', 5);

            return 0;
        }

        //Evita links inválidos
        if (!SpiderAsserts::isDocumentLink($link)) {
            $this->logger('Href refused', 'info', 5);
            return 0;
        }

        $this->logger('Check Cache for id:' . $link->getId('string'), 'info', 1);
        //Evita duplicidade
        if ($this->requests > 0 && $this->cache->isObject($link->getId('string'))) {
            $this->logger('cached', 'info', 5);
            $this->cached++;
            return 0;
        }
        $this->pool->save($link);

        return 1;
    }

    protected function collect(InterfaceLink $target, $withLinks = false)
    {
        $URI = $target->get('href');

        if($target instanceof InterfaceSubscription)
        {
            //check format1
        }

        try {
            if(!SpiderAsserts::isDocumentHref($URI)){
                $this->logger('URI wrong:[' . $URI . ']', 'err');
                $this->pool->errLink($target, 'invalid URL');
                return false;
            }

            try {
                $crawler = $this->getCrawler($URI);
            }
            catch(\Exception $e){
                $this->logger($e->getMessage(), 'err');
                if($this->requests === 0){
                    $this->errors++;
                    throw new \Exception ('Error in the first request:' . $e->getMessage());
                }
            }

            if (!isset($crawler)) {
                $this->logger('Crawler broken', 'err');
                $this->pool->errLink($target, 'impossible crawler');
                return false;
            }

            if (!$target instanceof InterfaceSubscription) {
                if(DocumentManager::isFresh($this->getBody(), $target, $this->getSubscription())){
                    $target->setDocument(clone $crawler, $this->getSubscription(), $this->transferDependency());
                    $this->logger('document IS fresh');
                }
                else{
                    $this->logger('document isnt fresh');
                }
            }

            $target->setStatus(1);//done!

            if ($withLinks) {
                $this->logger('go to the scan more links!', 'info', 5);
                try {
                    $target->set('hyperlinks', $this->collectLinks($crawler));
                }
                catch(\Exception $e) {
                    $this->logger($e->getMessage(), 'err');
                    $this->errors++;
                }
            }

            $this->logger('saving object on cache, with id:' . $target->getId('string'), 'info', 5);
            $this->pool->save($target);
            $this->success++;

            return true;
        }
        catch (\Zend\Http\Exception\InvalidArgumentException $e) {
            $this->logger( 'Invalid argument on [' . $URI . ']', 'err');
            $this->pool->errLink($target, 'invalid argument on HTTP request');
            $this->errors++;
            throw new \Exception ('Invalid argument');
        }
        catch (\Zend\Http\Client\Adapter\Exception\RuntimeException $e) {
            $this->logger( 'Http Client Runtime error on  [' . $URI . ']', 'err');
            $this->pool->errLink($target, 'Runtime error on Http Client Adaper');
            $this->errors++;
            return false;
        }

    }

    /**
     * @return int
     */
    protected function collectLinks($crawler)
    {
        $aCollection = $crawler->filter('a');

        if ($aCollection->count() < 1 && $this->requests === 0) {
            throw new \Exception('Error on collect links in the index');
        }

        $this->logger(
            'Number of links founded in request #' 
            . $this->requests . ':' . $aCollection->count()
        );

        foreach($aCollection as $node)
        {
            if($this->checkLimit()){
                $link = new Link($node);
                $this->hyperlinks +=  $this->addLink($link);
            }
        }

        return $aCollection->count();
    }

    protected function poolCollect($withLinks = false)
    {
        if (!$pool = $this->pool->getPool('collect')) {
            return false;
        }

        foreach ($pool as $link) {
            if (!$link instanceof InterfaceLink) {
                var_dump($pool);
                break;
            }

            if (!$this->checkLimit()) {
                $this->pool->errLink($link, 'Limit reached');
                break;
            }

            $this->logger("\n" . '====== Request number #' . $this->requests . '======');

            try{
                $this->collect($link, $withLinks);
            }
            catch(\Exception $e){
                $this->errors++;
                $this->logger('Can\'t collect:' . $e->getMessage(), 'err');
            }

            $this->logger($this->getResume());
        }
    }

    /**
     * Método principal que faz indexação
     */
    public function run(InterfaceSubscription $subscription)
    {

        $this->restart();
        $this->subscription = $subscription;
        $this->performLogin();
        $this->collect($this->subscription, true);

        $i = 0;
        while ($i < $this->subscription->getMaxDepth()) {
            $this->poolCollect(true);
            $i++;
        }

        $this->poolCollect(); //conclusion

        echo $this->getResume();

        return $this->pool->getPackage();
    }
}
