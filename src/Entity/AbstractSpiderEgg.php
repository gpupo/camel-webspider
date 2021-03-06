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

namespace Gpupo\CamelWebspider\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Este é um ovo :)
 * Ele abstrai métodos reusáveis entre a maior
 * parte dos objetos do projeto.
 */
class AbstractSpiderEgg extends ArrayCollection
{
    protected $cache;
    protected $config;
    protected $logger;
    protected $name;

    public function __construct(array $array, array $config = null)
    {
        if ($config) {
            $this->set('config', new DoctineArrayCollection($config));
        }

        parent::__construct($array);
    }

    protected function transferDependency()
    {
        return [
            'logger' => $this->logger,
            'cache'  => $this->cache,
            'config' => $this->config,
        ];
    }

    protected function getConfig($key, $defaultValue = null)
    {
        if (
            $this->config instanceof ArrayCollection
            && $config = $this->config->get($key)
        ) {
            return $config;
        }

        return $defaultValue;
    }

    /**
     * Debug, like var_dump, but output on log.
     */
    protected function debugger($object, $info = 'DEBUGGER')
    {
        return $this->logger(
            "\n"
            .$info
            .":\n"
            .var_export($object, true),
            'echo',
            1
        );
    }

    /**
     * @todo Lidar com níveis da configuração de cada componente
     */
    protected function logger($string, $type = 'info', $level = 1)
    {
        if ($type === 'echo') {
            echo $string;
            $type = 'info';
        }
        if (
            $this->logger
            && $this->getConfig('log_level', 3) >= $level
        ) {
            return $this->logger->$type(
                '#CamelSpider '.$this->name.':'.$string
            );
        }
    }
}
