<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks;

use Vibe\PortableText\Nodes\Node;
use function Vibe\PortableText\Nodes\node;

/**
 * @param  list<Node|string>|Node|string  $children
 */
function block(array|Node|string $children = []): Block
{
    return new Block($children);
}

function blankLine(): Block {
    return block();
}