<?php

declare(strict_types=1);

namespace pj\routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

final class RequestMatcherFactory implements RequestMatcherFactoryInterface
{
    private RouteCollection $routesCollection;

    public function __construct(RouteCollection $routeCollection)
    {
        $this->routesCollection = $routeCollection;
    }

    public function create(Request $request): RequestMatcherInterface
    {
        return new UrlMatcher(
            $this->routesCollection,
            (new RequestContext())->fromRequest($request)
        );
    }
}