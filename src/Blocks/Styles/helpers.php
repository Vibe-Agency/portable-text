<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\Styles;

use Vibe\PortableText\Blocks\Block;
use Vibe\PortableText\Nodes\Node;
use function Vibe\PortableText\Blocks\block;

/**
 * @param  list<Node|string>|Node|string  $children
 */
function heading1(array|Node|string $children = []): Block
{
    return block($children)->heading1();
}

/**
 * @param  list<Node|string>|Node|string  $children
 */
function heading2(array|Node|string $children = []): Block
{
    return block($children)->heading2();
}

/**
 * @param  list<Node|string>|Node|string  $children
 */
function heading3(array|Node|string $children = []): Block
{
    return block($children)->heading3();
}

/**
 * @param  list<Node|string>|Node|string  $children
 */
function heading4(array|Node|string $children = []): Block
{
    return block($children)->heading4();
}

/**
 * @param  list<Node|string>|Node|string  $children
 */
function heading5(array|Node|string $children = []): Block
{
    return block($children)->heading5();
}

/**
 * @param  list<Node|string>|Node|string  $children
 */
function heading6(array|Node|string $children = []): Block
{
    return block($children)->heading6();
}

/**
 * @param  list<Node|string>|Node|string  $children
 */
function quote(array|Node|string $children = []): Block
{
    return block($children)->quote();
}

/**
 * @param  list<Node|string>|Node|string  $children
 */
function div(array|Node|string $children = []): Block
{
    return block($children)->div();
}