<?php

namespace CamelSpider\Spider;
use CamelSpider\Entity\Link,
Zend\Uri\Uri;
class SpiderProcessor
{
	protected $goutte; 
    
	protected $logger;
	
	protected $cache;
	
	protected $subscription;
	/**
	* Recebe instância de https://github.com/fabpot/Goutte
	* e do Monolog
	**/
    	public function __construct($goutte, $logger)
    	{
      		$this->goutte = $goutte;
	    	$this->logger = $logger;
		    $this->cache  = new SpiderCache;
            return $this;
    	}	

	protected function logger($string, $type = 'info')
	{
		return $this->logger->$type('#CamelSpiderProcessor ' . $string);
	}
	public function debug()
	{
		var_dump($this->cache);
	}

	public function getPool()
	{
		return $this->cache->getPool();
	}

	public function getCrawler($URI, $mode = 'GET')
	{
		
		// reiniciar instancia ?
		//$this->goutte->insulate();
		$this->logger( 'created a Crawler for [' . $URI . ']');
	   	
		try {
			$client = $this->goutte->request($mode, $URI);
		}
		catch(\Zend\Http\Client\Adapter\Exception\TimeoutException $e)
		{
			$this->logger( 'faillure on create a crawler [' . $URI . ']', 'err');	
		}
		return $client;
		
	}


    protected function getRelevancy($raw)
    {
        return 0;
    }    


    /**
    * Processa o conteúdo do documento.
    * Neste momento são aplicados os filtros de conteúdo
    * e retornando flag se o conteúdo é relevante
    **/
	protected function getResponse()
	{
	    $response = array();	
        $response['raw'] = $this->goutte->getResponse();
        $response['relevancy'] = $this->getRelevancy($response['raw']);
           
        return $response;
        

	}
	protected function getDomain()
	{
		return $this->subscription->get('domain');
	}
	
	protected function saveLink(Link $link)
	{
		return $this->cache->set($link->get('href'), $link);
	}
	protected function isValidLink($href)
	{
		if(
			
			stripos($href, 'mail') == true ||
			
		 	empty($href) ||
		 
			substr($href, 0,10) == 'javascript' ||
		 
			substr($href, 0, 1) == '#'
		)
		{
			$this->logger('HREF descarted:[' . $href. ']', 'info');
			return false;
		}
		
		$zendUri = new Uri($href);
		if($zendUri->isValid()){
			return true;
		}
		$this->logger('HREF malformed:[' . $href. ']', 'info');
		return false;
	}
	
	protected function processAddLink($link)
	{
		//Evita duplicidade
		if($this->cache->containsKey($link->get('href'))){
			$this->logger('cached:[' . $link->get('href') . ']');
		    return false;
		}
		
		//Evita sair do escopo
		if(substr($link->get('href'), 0, 4) == 'http' && 
			stripos($link->get('href'), $this->getDomain()) === false
		){
			$this->logger('outside the scope of ['.$this->getDomain().']:[' . $link->get('href') . ']');
		    return false;
		}
		
		//Evita links inválidos
	  	if(!$this->isValidLink($link->get('href')))					
		{
			$this->logger('invalid link:[' . $link->get('href') . ']');
		    return false;
		}
		
		return $this->saveLink($link);
	}
	
    protected function collect($target, $withLinks = false)
    {
        $URI = $target->get('href');
		$this->logger( 'trying to collect links in [' . $URI . ']');
		try{
			if(!$this->isValidLink($URI)){
			    $this->logger('URI wrong:[' . $URI . ']', 'err');
			    return false;
			}	
			$crawler = $this->getCrawler($URI);
			
			$target->set('response', $this->getResponse());
		    
            if($withLinks){
                $target->set('linksCount', $this->collectLinks($crawler));
            }

            $this->saveLink($target);
        }
		catch(\Zend\Http\Exception\InvalidArgumentException $e)
		{
			$this->logger( 'Invalid argumento on [' . $URI . ']', 'err');
		}
        catch(\Zend\Http\Client\Adapter\Exception\RuntimeException $e)   
        {
			$this->logger( 'Http Client Runtime error on  [' . $URI . ']', 'err');
		}
    }
    
	protected function collectLinks($crawler)
	{
				
        $aCollection = $crawler->filter('a');
    
        $this->logger( 'Number of links founded:' . $aCollection->count());
        
        foreach($aCollection as $node)
        {
            
            $link = new Link($node);
            $this->processAddLink($link);
            
        }
        return  $aCollection->count();	
	}
    protected $recursive = 1;

    protected function poolCollect($withLinks = false)
    {
        foreach($this->getPool() as $link){
            $this->collect($link, $withLinks) ;
        }
    }    
	public function checkUpdates($subscription, $recursive = 0)
	{

		$this->subscription = $subscription;
		$this->collect($this->subscription, true);
		
        //coletando links e conteúdo
        $i = 0;
        while($i < $this->recursive){
            $this->poolCollect(true);
        }          

        //agora somente o conteúdo
		$this->poolCollect();	

		$this->debug();
	
	}
	
}
