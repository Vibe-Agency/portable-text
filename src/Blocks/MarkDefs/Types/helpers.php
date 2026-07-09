<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\MarkDefs\Types;

use Vibe\PortableText\Blocks\MarkDefs\MarkDef;
use function Vibe\PortableText\Blocks\MarkDefs\markDef;

function link(string $key, string $href, string $target = '_self'): MarkDef
{
    return markDef($key, Type::Link)
        ->customAttribute('href', $href)
        ->customAttribute('target', $target);
}