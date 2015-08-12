<?php

namespace Gpupo\CamelWebspider;

use Gpupo\CommonSdk\FactoryAbstract;

//use Gpupo\CamelWebspider\Client\Client;
use Gpupo\CamelWebspider\Spider\Indexer;
use Gpupo\CamelWebspider\Spider\AbstractCache;
use Goutte\Client;

class Factory extends FactoryAbstract
{
    public function setClient(array $clientOptions = [])
    {
        //$this->client = new Client($clientOptions, $this->logger);
        $this->client = new Indexer(new Client());
    }

    public function getNamespace()
    {
        return '\Gpupo\CamelWebspider\\';
    }

    protected function getSchema($namespace = null)
    {
        return [
        ];
    }
}
