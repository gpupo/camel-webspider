<?php

namespace CamelSpider\Entity;

use CamelSpider\Entity\AbstractSpiderEgg,
    Symfony\Component\DomCrawler\Crawler,
    Symfony\Component\BrowserKit\Response,
    CamelSpider\Spider\SpiderAsserts,
    CamelSpider\Spider\SpiderDom,
    CamelSpider\Entity\InterfaceSubscription,
    CamelSpider\Tools\Urlizer;

/**
 * Contain formated response
 *
 * @package     CamelSpider
 * @subpackage  Entity
 * @author      Gilmar Pupo <g@g1mr.com>
 *
 */


class Document extends AbstractSpiderEgg
{
    protected $name = 'Document';

    private $crawler;

    private $response;

    private $subscription;

    private $asserts;

    private $bigger = NULL;

    /**
     * Recebe a response HTTP e também dados da assinatura,
     * para alimentar os filtros que definem a relevânca do
     * conteúdo
     *
     * Config:
     *
     *
     * @param array $dependency Logger, Cache, array Config
     *
     **/
    public function __construct(Crawler $crawler, InterfaceSubscription $subscription, $dependency = NULL)
    {
        $this->crawler = $crawler;
        $this->subscription = $subscription;

        if($dependency){
            foreach(array('logger', 'cache') as $k){
                if(isset($dependency[$k])){
                    $this->$k = $dependency[$k];
                }
            }
        }
        $config = isset($dependency['config']) ? $dependency['config'] : NULL;
        parent::__construct(array('relevancy'=>0), $config);
        $this->processResponse();
    }

    protected function setTitle()
    {
        $title = $this->crawler->filter('title')->text();
        $this->set('title', trim($title));
        $this->logger('setting Title as [' . $this->getTitle() . ']');
    }

    public function getTitle()
    {
        return $this->get('title');
    }

    protected function getBody()
    {
        return $this->crawler->filter('body');
    }

    protected function getRaw()
    {
        if ($this->getBody() instanceof DOMElement) {
            return SpiderDom::toHtml($this->getBody());
        }
    }

    /**
     * Faz query no documento, de acordo com os parâmetros definidos
     * na assinatura e define a relevância, sendo que esta relevância 
     * pode ser:
     *  1) Possivelmente contém conteúdo
     *  2) Contém conteúdo e contém uma ou mais palavras chave desejadas 
     *  pela assinatura ou não contém palavras indesejadas
     *  3) Contém conteúdo, contém palavras desejadas e não contém 
     *  palavras indesejadas
     **/
    protected function setRelevancy()
    {
        if(!$this->bigger)
        {
            $this->logger('Content too short');
            return false;
        }
        $this->addRelevancy();

        $txt = $this->getTitle() . "\n"  . $this->getText();

        //Contain?
        if(SpiderAsserts::containKeywords($txt, $this->subscription->getFilter('contain'))) {
            $this->addRelevancy();
        } else {
            $this->logger('Document not contain keywords');
        }
        //Not Contain?
        if(!SpiderAsserts::containKeywords($txt, $this->subscription->getFilter('notContain'), false)) {
            $this->addRelevancy();
        } else {
            $this->logger('Document contain bad keywords');
        }
    }

    protected function addRelevancy()
    {
        $this->set('relevancy', $this->get('relevancy') + 1);
        $this->logger('Current relevancy:'. $this->getRelevancy());
    }

    /**
     * localiza a tag filha de body que possui maior
     * quantidade de texto
     */
    protected function searchBiggerInTags($tag)
    {
        $data = $this->crawler->filter($tag);

        foreach(clone $data as $node)
        {
            if(SpiderDom::containerCandidate($node)){
                $this->bigger = SpiderDom::getGreater($node, $this->bigger);
            }
        }
    }

    protected function getBiggerTag()
    {
        foreach(array('div', 'td', 'span') as $tag){
            $this->searchBiggerInTags($tag);
        }
        if(! $this->bigger instanceof \DOMElement ) {
            $this->logger('Cannot find bigger', 'err');
            return false;
        }
    }

    protected function saveBiggerToFile()
    {
        $title = '# '. $this->getTitle() . "\n\n";
        $this->cache->saveToHtmlFile($this->getHtml(), $this->get('slug'));
        $this->cache->saveDomToTxtFile($this->bigger, $this->get('slug'), $title);
    }

    public function getHtml()
    {
        if ($this->bigger) {
            return SpiderDom::toHtml($this->bigger);
        }
    }

    /**
     * Converte o elemento com maior probabilidade de
     * ser o container do conteúdo em plain text
     */
    protected function setText()
    {
        if($this->bigger){
            $this->set('text', SpiderDom::toText($this->bigger));
        }
        else
        {
            $this->set('text', NULL);
        }
    }

    public function getText()
    {
        return $this->get('text');
    }

    protected function setSlug()
    {
        $this->set('slug', substr(Urlizer::urlize($this->get('title')), 0, 30));
    }

    protected function processResponse()
    {
        $this->logger('processing');
        $this->setTitle();
        $this->setSlug();
        $this->getBiggerTag();

        if ($this->getConfig('save_document', false)) {
            $this->saveBiggerToFile();
        }

        $this->setText();
        $this->setRelevancy();
    }

    /**
    * Verificar data container se link já foi catalogado.
    * Se sim, fazer idiff e rejeitar se a diferença for inferior a x%
    * Aplicar filtros contidos em $this->subscription
    **/
    public function getRelevancy()
    {
        return $this->get('relevancy');
    }


    /**
     * reduce memory usage
     *
     * @return self minimal
     */
    public function toPackage()
    {
         $array = array(
            'relevancy' => $this->getRelevancy(),
            'title'     => $this->getTitle(),
            'text'      => $this->getText(),
            'html'      => $this->getHtml(),
            'raw'       => $this->getRaw()
        );

        return $array;
    }

    /**
     * @return array $array
     */
    public function toArray()
    {
        $array = array(
            'relevancy' => $this->getRelevancy(),
            'title'     => $this->getTitle(),
        );

        return $array;
    }
}
