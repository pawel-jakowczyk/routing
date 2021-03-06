# PJ routing middleware

[![Tests](https://github.com/pawel-jakowczyk/routing/actions/workflows/php.yml/badge.svg)](https://github.com/pawel-jakowczyk/routing/actions/workflows/php.yml)

This repository holds the RoutingMiddleware which implements the Psr\Http\Server\MiddlewareInterface.
It can be instantiated by create method which requires symfony RouteCollection,
or it can be instantiated by constructor which requires the RequestMatcherInterface object.

## Instalation

    composer require pawel-jakowczyk/routing

## Usage

```php
use Laminas\Diactoros\ServerRequest;
use PJ\Routing\RoutingMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Routing\RouteCollection;

$middleware = RoutingMiddleware::create(new RouteCollection());
$middleware->process(
    new ServerRequest(),
    new class() implements RequestHandlerInterface
    {
        public function handle(ServerRequestInterface $request): ResponseInterface
        {
            return new Response();
        }
    }
);

```
