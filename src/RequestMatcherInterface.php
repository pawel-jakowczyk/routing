<?php

declare(strict_types=1);

namespace pj\routing;

use Psr\Http\Message\ServerRequestInterface;

interface RequestMatcherInterface
{
    public function match(ServerRequestInterface $request): array;
}