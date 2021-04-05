<?php

declare(strict_types=1);

namespace pj\routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class RoutingMiddleware implements MiddlewareInterface
{
    private $routesCollection;

    public function __construct(RouteCollection $routesCollection)
    {
        $this->routesCollection = $routesCollection;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $symfonyRequest = (new HttpFoundationFactory())->createRequest($request);
        $matcher = new UrlMatcher(
            $this->routesCollection,
            (new RequestContext())->fromRequest($symfonyRequest)
        );
        $routingData = $matcher->match($symfonyRequest->getPathInfo());
        $request = $request->withAttribute(
            'middlewares',
            $routingData['middlewares'] ?? []
        );
        return $handler->handle($request);
    }
}