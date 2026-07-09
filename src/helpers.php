<?php

declare(strict_types=1);

namespace Vibe\PortableText;

use Vibe\PortableText\Blocks\Block;
use Vibe\PortableText\Nodes\Node;

/**
 * @param  list<Block|Node|string|null>|Block|Node|string  $blocks
 */
function text(array|Block|Node|string $blocks = [], string $key = 'text'): Text
{
    return new Text($blocks, $key);
}