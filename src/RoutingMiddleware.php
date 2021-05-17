<?php

declare(strict_types=1);

namespace PJ\Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Routing\RouteCollection;

final class RoutingMiddleware implements MiddlewareInterface
{
    private RequestMatcherInterface $requestMatcher;

    public static function create(RouteCollection $routeCollection): self
    {
        return new self(
            new RequestMatcher(
                new HttpFoundationFactory(),
                $routeCollection
            )
        );
    }

    public function __construct(
        RequestMatcherInterface $requestMatcher
    ) {
        $this->requestMatcher = $requestMatcher;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        foreach ($this->requestMatcher->match($request) as $key => $data) {
            $request = $request->withAttribute($key, $data);
        }
        return $handler->handle($request);
    }
}