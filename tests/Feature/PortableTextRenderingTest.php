<?php

declare(strict_types=1);

namespace Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sanity\BlockContent;
use Vibe\PortableText\Nodes\Marks\Mark;
use function Vibe\PortableText\Blocks\blankLine;
use function Vibe\PortableText\Blocks\block;
use function Vibe\PortableText\Blocks\MarkDefs\Types\link;
use function Vibe\PortableText\Nodes\node;
use function Vibe\PortableText\text;

class PortableTextRenderingTest extends TestCase
{
    #[Test]
    public function itRendersSimpleText(): void
    {
        $text = text('Hello, World!');

        $this->assertSame('<p>Hello, World!</p>', BlockContent::toHtml($text->toArray()));
    }

    #[Test]
    public function itRendersComplexText(): void
    {
        $text = text([
            'Hello, World!',
            blankLine(),
            block([
                node('T'),
                node('h')->marks([Mark::Emphasis, Mark::Strong]),
                node('i')->marks([Mark::Emphasis]),
                node('s')->marks([Mark::Emphasis, Mark::Strong]),
                ' ',
                node('text ')->marks([Mark::Underline]),
                node('is')->marks([Mark::Underline, Mark::Strong]),
                node(' all sorts ')->marks([Mark::Code]),
                'of ',
                node('weird')->marks([Mark::Underline]),
            ]),
        ]);

        $this->assertSame(
            '<p>Hello, World!</p>'
            .'<p></p>'
            .'<p>T<em><strong>h</strong>i<strong>s</strong></em> '
            .'<span style="text-decoration: underline;">text <strong>is</strong></span>'
            .'<code> all sorts </code>'
            .'of '
            .'<span style="text-decoration: underline;">weird</span></p>',
            BlockContent::toHtml($text->toArray())
        );
    }

    #[Test]
    public function itRendersLinks(): void
    {
        $link = link('a-link', 'https://example.com', '_blank');
        $text = text([
            node('Hello, World!')->mark($link),
        ]);

        $this->assertSame(
            '<p><a href="https://example.com">Hello, World!</a></p>',
            BlockContent::toHtml($text->toArray())
        );
    }
}
