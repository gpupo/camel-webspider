![Camel
Spider](http://www.camel-spiders.net/images/camel-spider-head.jpg)

``camel-webspider`` é um ([Web crawler](http://en.wikipedia.org/wiki/Web_spider)) que coleta links e conteúdo de sites.

        Exemplo de uso da CamelSpider:
        Portal de notícias quer coletar novos documentos vindos de
        fontes conhecidas de notícias, então, a partir de um cadastro destas
        fontes, CamelSpider coleta estas novas notícias e armazena na base de
        dados do portal.


Obtém um documento em texto plano
que reflete o conteúdo principal de cada conteúdo indexado.

Você pode fazer um leitor de Feed para sites que não possuem Feed!

## Filtros

Esse conteúdo é filtrado a partir de palavras chaves.

A spider navega nestes sites, coleta os links que fazem parte do escopo do domínio e faz o processamento interno dessa informação.

De acordo com os filtros, cada documento coletado recebe uma avaliação
de relevância, sendo que esta pontuação pode ser:

 * 0) não contém conteúdo
 * 1) Possivelmente contém conteúdo
 * 2) Contém conteúdo e contém uma ou mais palavras chave desejadas pela assinatura ou não contém palavras indesejadas
 * 3) Contém conteúdo, contém palavras desejadas e não contém palavras indesejadas

## Estrutura

A Camel Webspider é um componente a ser utilizado por uma aplicação que gerencie a base de assinaturas e receba o retorno do processamento para armazenamento em banco de dados.

O [Camel Spider Bundle](http://github.com/gpupo/CamelSpiderBundle) integra a Camel Webspider ao Symfony 2 e gerencia as assinaturas e o cache de informações e neste projeto complementar você pode visualizar a implementação de componentes que a CamelSpider utiliza como dependency injection mas não os implementa por ser fora de seu escopo.

## Config

        camelSpider:
            save_document:  false | true
            memory_limit:   80 (MB)
            requests_limit: 300
            log_level:      1~5
            minimal_relevancy: 3

## Otimização e cache

A cada requisição é consumido memória.
Para evitar isso, os objetos são cacheados em disco, portanto é
necessário informar um objeto cache onde seja possível gravar e
recuperar informações a partir de um hash.


O objeto gravado é serializado pelo componente [Zend Cache](http://framework.zend.com/manual/en/zend.cache.html).
Este objeto é passado ao construtor da Camel Webspider (Dependency
injection).

Você pode implementar este serviço de cache mas também pode utilizar o  [Camel Spider Bundle](http://github.com/gpupo/CamelSpiderBundle) como referência de implementação.


Os objetos capturados são retornados pelo processamento inicial, e são
acessados diretamente do cache, pela aplicação que os utiliza.

## Dependências

* Symfony Components: BrowserKit, ClassLoader, CssSelector, DomCrawler, Finder, and Process
* Zend Framework 2 libraries: Cache, Date, Uri, Http, and Validate
* PEAR [Text_Diff -maybe!](http://pear.php.net/package/Text_Diff)


## Instalação

Linux / Mac OS X

    sudo curl -LsS https://github.com/gpupo/camel-webspider/releases/download/1.1/camel-webspider.phar -o /usr/local/bin/camel-webspider;
    sudo chmod a+x /usr/local/bin/camel-webspider;

Windows

    c:\> php -r "readfile('https://github.com/gpupo/camel-webspider/releases/download/1.1/camel-webspider.phar');" > camel-webspider


Bastará executar ``camel-webspider``.

## Comando

    camel-webspider find -w www.example.com -k interest

## Instalação como biblioteca

Adicione o pacote ``camel-webspider`` ao seu projeto utilizando [composer](http://getcomposer.org):

    composer require camel-webspider

---

## Você pode contribuir com o projeto!

Este README precisa de tradução e ainda temos muito trabalho pela frente e gostaríamos da sua ajuda.

Coisas para fazer:

    - Criar sandbox para facilitar os testes
    - Criar testes unitários
    - Melhorar a documentação em Inglês
    - Corrigir coding standards

### Desenvolvedores que contribuem com este projeto

- [@gpupo](https://github.com/gpupo)
- [All Contributors](https://github.com/gpupo/cnova-sdk/contributors)


## Documentação

Este projeto utiliza idéias e conceitos de projetos existentes, sendo eles:

* [Swish-e - Pearl](http://swish-e.org/docs/spider.html)


## Licença

MIT, see [LICENSE](https://github.com/gpupo/camel-webspider/blob/master/LICENSE).

---
