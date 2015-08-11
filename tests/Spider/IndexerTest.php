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

use Goutte\Client;
use Zend\Http\Client as Zend_Http_Client;

class IndexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerAuth()
     */
    public function testAuthToArray($auth, $len)
    {
        $spider = new Indexer();
        $auto = 3;
        $this->AssertEquals($len + $auto, count($spider->getAuthCredentials($auth)));
    }

    /**
     * @dataProvider providerAuth()
     */
    public function testLoginForm($auth)
    {
        $spider = new Indexer();
        $credentials = $spider->getAuthCredentials($auth);

        $this->assertArrayHasKey('type', $credentials);

        foreach ($spider->loginFormRequirements() as $r) {
            if (!array_key_exists($r, $credentials)) {
                $this->setExpectedException('Exception');
                $spider->loginForm($credentials);
            }
        }
    }

    /**
     * @dataProvider providerHellCookies()
     */
    public function testZendClient($url)
    {
        $client = new Zend_Http_Client();
        $client->setUri($url);
        //$response = $client->request();

        //var_dump($response);
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @dataProvider providerHellCookies()
     */
    public function testCookiesHell($url)
    {
        $client = new Client();
        $crawler = $client->request('GET', $url);
        $request  = $client->getRequest();
        $response = $client->getResponse();

        //var_dump($response);
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function ProviderHellCookies()
    {
        return [
            ['http://www.agricultura.gov.br/comunicacao/noticias/'],
        ];
    }

    /**
     * @dataProvider providerNavigation()
     */
    public function testNavigation($host, $paths)
    {
        $client = new Client();
        //Test with absolute path
        foreach ($paths as $path) {
            $uri = 'http://'.$host.$path;
            $crawler = $client->request('GET', $uri);
            $this->assertEquals(200, $client->getResponse()->getStatus());
            $this->assertEquals($uri, $client->getRequest()->getUri());
        }
        //Test with relative path and get absolute URI
        foreach ($paths as $path) {
            $uri = 'http://'.$host.$path;
            $crawler = $client->request('GET', $path);
            $this->assertEquals(200, $client->getResponse()->getStatus());
            $this->assertEquals($uri, $client->getRequest()->getUri());
        }
    }

    /**
     * @dataProvider providerNavigation()
     */
    public function testWrongNavigation($host, $paths)
    {
        $client = new Client();
        //Test with absolute path
        $uri = 'http://'.$host.'/some'.rand();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(404, $client->getResponse()->getStatus());
    }

    public function providerNavigation()
    {
        $a = [];

        $a[] = [
            'host'  => 'diversao.terra.com.br',
            'paths' => ['/', '/tv/'],
        ];

        $a[] = [
            'host'  => 'www.mozilla.org',
            'paths' => ['/en-US/firefox/new/', '/en-US/firefox/features/', '/en-US/mobile/faq/'],
        ];

        return $a;
    }

    public function providerAuth()
    {
        $s = '';
        $i = 0;
        $a = [];
        foreach (['something', 'button', 'username', 'password', 'expected'] as $n) {
            $s .= '"'.$n.'":"'.$n.'"'."\n";
            $i++;
            $a[] = [$s, $i];
        }

        return $a;
    }
}
