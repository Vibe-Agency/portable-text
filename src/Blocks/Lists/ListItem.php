<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\Lists;

enum ListItem: string
{
    case Bullet = 'bullet';
    case Number = 'number';
}
