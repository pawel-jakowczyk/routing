<?php

declare(strict_types=1);

namespace pj\routing;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

final class RequestMatcher implements RequestMatcherInterface
{
    private HttpFoundationFactoryInterface $httpFoundationFactory;
    private RouteCollection $routeCollection;

    public function __construct(
        HttpFoundationFactoryInterface $httpFoundationFactory,
        RouteCollection $routeCollection
    ) {
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->routeCollection = $routeCollection;
    }

    public function match(ServerRequestInterface $request): array
    {
        $symfonyRequest = $this->httpFoundationFactory->createRequest($request);
        $matcher = new UrlMatcher(
            $this->routeCollection,
            (new RequestContext())->fromRequest($symfonyRequest)
        );
        return $matcher->matchRequest($symfonyRequest);
    }
}