<?php

declare(strict_types=1);

namespace pj\routing\tests;

use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;
use pj\routing\RoutingMiddleware;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingMiddlewareTest extends TestCase
{
    /**
     * @test
     */
    public function itThrowsNoConfigurationExceptionWhenNoRoutesAreConfigured()
    {
        $this->expectException(NoConfigurationException::class);
        $middleware = new RoutingMiddleware(new RouteCollection());
        $middleware->process(
            ServerRequestFactory::fromGlobals(),
            $this->createMock(RequestHandlerInterface::class)
        );
    }

    /**
     * @test
     */
    public function itMatchesWithTheRequestToFindTheRightRoute()
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('home', new Route('/'));
        $middleware = new RoutingMiddleware($routeCollection);
        $middleware->process(
            ServerRequestFactory::fromGlobals(),
            $this->createMock(RequestHandlerInterface::class)
        );
    }

    /**
     * @test
     */
    public function itSetsEmptyMiddlewareAttributeWhenNoMiddlewareDeclaredInRouting()
    {

    }

    /**
     * @test
     */
    public function itPassesTheMiddlewaresFromRoutingUnderMiddlewaresAttribute()
    {

    }
}