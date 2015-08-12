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

namespace Gpupo\Tests\CamelWebspider\Spider;

class SpiderAssertsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerDocumentHref
     */
    public function testValidDocumentHref($input)
    {
        $this->assertTrue(SpiderAsserts::isDocumentHref($input));
    }

    /**
     * @dataProvider providerInvalidDocumentHref
     */
    public function testInvalidDocumentHref($input)
    {
        $this->assertFalse(SpiderAsserts::isDocumentHref($input));
    }

    public function providerDocumentHref()
    {
        return [
            ['magica.html'],
            ['http://www.gpupo.com/about'],
            ['/var/dev/null.html'],
        ];
    }

    public function providerInvalidDocumentHref()
    {
        return [
            ['mailto:g@g1mr.com'],
            ['javascript("void(0)")'],
            ['#hashtag'],
        ];
    }

    /**
     * @dataProvider providerContainKeywords
     */
    public function testContainKeywords($txt, $word)
    {
        $this->assertTrue(SpiderAsserts::containKeywords($txt, $word, true));
    }

    /**
     * @dataProvider providerNotContainKeywords
     */
    public function testNotContainKeywords($txt, $word)
    {
        $this->assertFalse(SpiderAsserts::containKeywords($txt, $word, false));
    }

    /**
     * @dataProvider providerContainKeywords
     */
    public function testContainBadKeywords($txt, $word)
    {
        $this->assertTrue(SpiderAsserts::containKeywords($txt, $word, false));
    }

    public function testContainNull()
    {
        $this->assertTrue(SpiderAsserts::containKeywords('Somewhere in her smile she knows', null));
        $this->assertTrue(SpiderAsserts::containKeywords('Somewhere in her smile she knows', []));
        $this->assertFalse(SpiderAsserts::containKeywords('Somewhere in her smile she knows', null, false));
        $this->assertFalse(SpiderAsserts::containKeywords('Somewhere in her smile she knows', [], false));
    }

    public function providerContainKeywords()
    {
        $array = [
            ['Something in the way she moves', ['way']],
            ['Attracts me like no other lover', ['lover']],
            ['Something in the way she woos me',['something']],
            ['I dont want to leave her now', ['other', 'want']],
        ];

        //words
        foreach (explode(' ', $this->getBigText()) as $word) {
            $array[] = [$this->getBigText(),['constituinte', 'firebug', 'metallica', $word]];
        }

        //half words
        foreach (explode(' ', 'cordos teresse mum rust niciati cida') as $word) {
            $array[] = [$this->getBigText(),['constituinte', 'firebug', 'metallica', $word]];
        }

        return $array;
    }

    public function providerNotContainKeywords()
    {
        return [
            ['Something in the way she moves', ['love', 'sex']],
            ['Attracts me like no other lover', ['bullet', 'gun']],
            ['Something in the way she woos me',['route', 'bad']],
            ['I dont want to leave her now', ['other', 'past']],
            ['You know I believe and how', []],
        ];
    }

    private function getBigText()
    {
        return <<<EOF
O documento foi assinado pelo presidente da Fapesp, Celso Lafer, e pelo diretor da GSK para a América Latina e o Caribe, Rogério Rocha Ribeiro.
A cerimônia teve ainda a participação do ministro da Saúde do Reino Unido, Simon Burns, do diretor-presidente da Fapesp, Ricardo Renzo Brentani,
e do cônsul-geral britânico, John Dodrell.
A colaboração foi estabelecida no âmbito do Projeto Trust in Science, iniciativa internacional do laboratório que também envolve,
no país, o Conselho Nacional de Desenvolvimento Científico e Tecnológico (CNPq).
"Há alguns anos a FAPESP se empenha na dimensão da internacionalização, seja por meio do aumento das cooperações com o setor privado,
seja a partir de acordos de interesse comum entre nações.
EOF;
    }
}
