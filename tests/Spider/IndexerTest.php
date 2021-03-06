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

namespace Gpupo\Tests\CamelWebspider\Spider;

use Goutte\Client;
use Gpupo\CamelWebspider\Spider\Indexer;
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
        return [[
            'host'  => 'diversao.terra.com.br',
            'paths' => ['/tv/', '/gente/'],
        ]];
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
