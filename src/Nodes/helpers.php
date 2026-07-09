<?php

declare(strict_types=1);

namespace Vibe\PortableText\Nodes;

function node(string $text): Node
{
    return new Node($text);
}