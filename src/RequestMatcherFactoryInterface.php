<?php

namespace pj\routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

interface RequestMatcherFactoryInterface
{
    public function create(Request $request): RequestMatcherInterface;
}