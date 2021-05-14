<?php

declare(strict_types=1);

namespace pj\routing\tests;

use Closure;
use Laminas\Diactoros\ServerRequest;
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
        $middleware = RoutingMiddleware::create(new RouteCollection());
        $middleware->process(
            ServerRequestFactory::fromGlobals(),
            $this->createMock(RequestHandlerInterface::class)
        );
    }

    /**
     * @test
     * @dataProvider routeExpectations
     */
    public function itMatchesWithTheRequestToFindTheRightRoute(ServerRequestInterface $serverRequest, array $middlewares)
    {
        $middleware = RoutingMiddleware::create($this->createRouteCollection());
        $middleware->process(
            $serverRequest,
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
            [new ServerRequest(), ['home-middleware']],
            [new ServerRequest([], [], '/test', 'GET'), ['test-middleware']],
            [new ServerRequest([], [], '/test/123'), ['test-123-middleware']],
            [new ServerRequest([], [], '/test', 'PUT'), ['test-put-middleware']],
            [new ServerRequest([], [], '/test', 'POST'), ['test-post-middleware']],
        ];
    }

    private function createRouteCollection(): RouteCollection
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('home', new Route('/', ['middlewares' => ['home-middleware']]));
        $routeCollection->add('test', new Route('/test', ['middlewares' => ['test-middleware']], [], [], '', [], ['GET']));
        $routeCollection->add('test-123', new Route('/test/123', ['middlewares' => ['test-123-middleware']]));
        $routeCollection->add('test-put', new Route('/test', ['middlewares' => ['test-put-middleware']], [], [], '', [], ['PUT']));
        $routeCollection->add('test-post', new Route('/test', ['middlewares' => ['test-post-middleware']], [], [], '', [], ['POST']));
        return $routeCollection;
    }
}