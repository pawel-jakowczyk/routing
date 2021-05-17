<?php

declare(strict_types=1);

namespace PJ\Routing;

use Psr\Http\Message\ServerRequestInterface;

interface RequestMatcherInterface
{
    public function match(ServerRequestInterface $request): array;
}