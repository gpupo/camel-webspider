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

namespace Gpupo\CamelWebspider\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Link extends ArrayCollection implements InterfaceLink
{
    public function __construct($node = null)
    {
        $link = [];

        if (!is_null($node) && $node instanceof \DOMElement) {
            $link = [
                'href' => $node->getAttribute('href'),
                'rel'  => $node->getAttribute('rel'),
            ];
        } elseif (is_string($node)) {
            $link['href'] = $node;
        }

        $link['status'] = 0;
        parent::__construct($link);
    }

    public function getHref()
    {
        return $this->get('href');
    }

    public function getRel()
    {
        return $this->get('rel');
    }

    /**
     * Gera o hash para armazenar em cache.
     **/
    public function getId($mode = null)
    {
        return sha1($this->get('href'));
    }

    public function isDone()
    {
        return  ($this->get('status') === 1) ? true : false;
    }

    public function isWaiting()
    {
        return  ($this->get('status') === 0) ? true : false;
    }

    public function setDocument($uri, $response,
        $subscription, array $dependency = null
    ) {
        $this->set('document', new Document(
            $uri, $response, $subscription, $dependency
        ));
    }

    public function getDocument()
    {
        return $this->get('document');
    }

    public function setStatus($x)
    {
        return $this->set('status', $x);
    }

    /**
     * reduce memory usage.
     */
    public function toMinimal()
    {
        if ($this->getDocument() instanceof Document) {
            $this->removeElement('document');
        }

        return $this;
    }

    public function toPackage()
    {
        if ($this->getDocument() instanceof Document) {
            $this->set('document', $this->getDocument()->toPackage());
        }

        return $this;
    }

    public function toArray()
    {
        return $this->toMinimal()->toArray();
    }
}
