<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\MarkDefs\Types;

enum Type: string
{
    case Link = 'link';
    case Comment = 'comment';
}
