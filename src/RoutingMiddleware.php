<?php

declare(strict_types=1);

namespace pj\routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Component\Routing\RouteCollection;

final class RoutingMiddleware implements MiddlewareInterface
{
    private HttpFoundationFactoryInterface $httpFoundationFactory;
    private RequestMatcherFactoryInterface $requestMatcherFactory;

    public static function create(RouteCollection $routesCollection): self
    {
        return new self(
            new HttpFoundationFactory(),
            new RequestMatcherFactory($routesCollection)
        );
    }

    public function __construct(
        HttpFoundationFactoryInterface $httpFoundationFactory,
        RequestMatcherFactoryInterface $RequestMatcherFactory
    ) {
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->requestMatcherFactory = $RequestMatcherFactory;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $symfonyRequest = $this->httpFoundationFactory->createRequest($request);
        $matcher = $this->requestMatcherFactory->create($symfonyRequest);
        $routingData = $matcher->matchRequest($symfonyRequest);
        foreach ($routingData as $key => $data) {
            $request = $request->withAttribute($key, $data);
        }
        return $handler->handle($request);
    }
}