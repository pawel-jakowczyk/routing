<?php

declare(strict_types=1);

namespace pj\routing\tests;

use Closure;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response;
use PHPUnit\Framework\TestCase;
use pj\routing\RoutingMiddleware;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
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
     * @dataProvider routeExpectations
     */
    public function itMatchesWithTheRequestToFindTheRightRoute(string $routeName, Route $route, array $middlewares)
    {
        $middleware = new RoutingMiddleware($this->createRouteCollection());
        $middleware->process(
            ServerRequestFactory::fromGlobals(),
            $this->createRequestHandler(
                fn (ServerRequestInterface $request) =>
                $this->assertEquals($middlewares, $request->getAttributes()['middlewares'])
            )
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

    private function createRequestHandler(Closure $checkExpectations): RequestHandlerInterface
    {
        return new class($checkExpectations) implements RequestHandlerInterface
        {
            private Closure $checkExpectations;

            public function __construct(Closure $checkExpectations)
            {
                $this->checkExpectations = $checkExpectations;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $checkExpectations = $this->checkExpectations;
                $checkExpectations($request);
                return new Response();
            }
        };
    }

    public function routeExpectations(): array
    {
        return [
            ['home', new Route('/'), []]
        ];
    }

    private function createRouteCollection(): RouteCollection
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('home', new Route('/'));
        return $routeCollection;
    }
}