<?php

declare(strict_types=1);

namespace pj\routing\tests;

use Closure;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response;
use PHPUnit\Framework\TestCase;
use pj\routing\RoutingMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Routing\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RoutingMiddlewareTest extends TestCase
{
    /**
     * @test
     * @dataProvider routeExpectations
     */
    public function itMatchesWithTheRequestToFindTheRightRoute(
        ServerRequestInterface $serverRequest,
        array $attributes
    ): void {
        RoutingMiddleware::create($this->createRouteCollection())->process(
            $serverRequest,
            $this->createRequestHandler(
                fn (ServerRequestInterface $request) =>
                $this->assertEquals($attributes, $request->getAttributes())
            )
        );
    }

    /**
     * @test
     */
    public function itThrowsNoConfigurationExceptionWhenNoRoutesAreConfigured()
    {
        $this->expectException(NoConfigurationException::class);
        RoutingMiddleware::create(new RouteCollection())->process(
            ServerRequestFactory::fromGlobals(),
            $this->createMock(RequestHandlerInterface::class)
        );
    }

    /**
     * @test
     */
    public function itThrowsResourceNotFoundExceptionWhenNoRouteMatches(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        RoutingMiddleware::create($this->createRouteCollection())->process(
            new ServerRequest([], [], '/wrong-path'),
            $this->createMock(RequestHandlerInterface::class)
        );
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
            [
                new ServerRequest(),
                ['key' => 'value-1', '_route' => 'home']
            ],
            [
                new ServerRequest([], [], '/test', 'GET'),
                ['key' => 'value-2', '_route' => 'test']
            ],
            [
                new ServerRequest([], [], '/test/123'),
                ['key' => 'value-3', '_route' => 'test-123']
            ],
            [
                new ServerRequest([], [], '/test', 'PUT'),
                ['key' => 'value-4', '_route' => 'test-put']
            ],
            [
                new ServerRequest([], [], '/test', 'POST'),
                ['key' => 'value-5', '_route' => 'test-post']
            ],
        ];
    }

    private function createRouteCollection(): RouteCollection
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add(
            'home',
            new Route('/', ['key' => 'value-1'])
        );
        $routeCollection->add(
            'test',
            new Route('/test', ['key' => 'value-2'], [], [], '', [], ['GET'])
        );
        $routeCollection->add(
            'test-123',
            new Route('/test/123', ['key' => 'value-3'])
        );
        $routeCollection->add(
            'test-put',
            new Route('/test', ['key' => 'value-4'], [], [], '', [], ['PUT'])
        );
        $routeCollection->add(
            'test-post',
            new Route('/test', ['key' => 'value-5'], [], [], '', [], ['POST'])
        );
        return $routeCollection;
    }
}