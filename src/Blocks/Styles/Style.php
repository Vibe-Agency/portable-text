<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\Styles;

enum Style: string
{
    case Normal = 'normal';
    case Heading1 = 'h1';
    case Heading2 = 'h2';
    case Heading3 = 'h3';
    case Heading4 = 'h4';
    case Heading5 = 'h5';
    case Heading6 = 'h6';
    case Quote = 'blockquote';
    case Div = 'div';
}
