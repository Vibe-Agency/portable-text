<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\MarkDefs;

use Vibe\PortableText\Blocks\MarkDefs\MarkDef;
use Vibe\PortableText\Blocks\MarkDefs\Types\Type;

function markDef(string $key, Type|string $type): MarkDef
{
    return new MarkDef($key, $type);
}