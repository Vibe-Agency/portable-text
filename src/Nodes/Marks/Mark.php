<?php

declare(strict_types=1);

namespace Vibe\PortableText\Nodes\Marks;

enum Mark: string
{
    case Strong = 'strong';
    case Emphasis = 'em';
    case Strikethrough = 'strike-through';
    case Underline = 'underline';
    case Code = 'code';
    case Subscript = 'subscript';
    case Superscript = 'superscript';
}
