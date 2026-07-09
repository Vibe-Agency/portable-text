<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\Lists;

use Vibe\PortableText\Blocks\Block;
use Vibe\PortableText\Blocks\Lists\ListItem;
use Vibe\PortableText\Nodes\Node;
use function Vibe\PortableText\Blocks\block;

/**
 * @param  list<Node|string>|Node|string  $children
 */
function bullet(array|Node|string $children = [], int $level = 1): Block
{
    return block($children)
        ->listItem(ListItem::Bullet)
        ->level($level);
}

/**
 * @param  list<Node|string>|Node|string  $children
 */
function numberBullet(array|Node|string $children = [], int $level = 1): Block
{
    return block($children)
        ->listItem(ListItem::Number)
        ->level($level);
}
