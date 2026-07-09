<?php

declare(strict_types=1);

namespace Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\AssertsPortableText;
use Vibe\PortableText\Nodes\Marks\Mark;
use function Vibe\PortableText\Blocks\blankLine;
use function Vibe\PortableText\Blocks\block;
use function Vibe\PortableText\Blocks\Lists\bullet;
use function Vibe\PortableText\Blocks\Lists\numberBullet;
use function Vibe\PortableText\Blocks\MarkDefs\Types\link;
use function Vibe\PortableText\Blocks\Styles\div;
use function Vibe\PortableText\Blocks\Styles\heading1;
use function Vibe\PortableText\Blocks\Styles\heading2;
use function Vibe\PortableText\Blocks\Styles\heading3;
use function Vibe\PortableText\Blocks\Styles\heading4;
use function Vibe\PortableText\Blocks\Styles\heading5;
use function Vibe\PortableText\Blocks\Styles\heading6;
use function Vibe\PortableText\Blocks\Styles\quote;
use function Vibe\PortableText\Nodes\node;
use function Vibe\PortableText\text;

class PortableTextValidityTest extends TestCase
{
    use AssertsPortableText;

    #[Test]
    public function itProducesValidSimpleText(): void
    {
        $this->assertValidPortableText(text('Hello, World!')->toArray());
    }

    #[Test]
    public function itProducesValidEmptyBlock(): void
    {
        $this->assertValidPortableText(text([blankLine()])->toArray());
    }

    #[Test]
    public function itProducesValidHeadings(): void
    {
        $this->assertValidPortableText(text([
            heading1('H1'),
            heading2('H2'),
            heading3('H3'),
            heading4('H4'),
            heading5('H5'),
            heading6('H6'),
        ])->toArray());
    }

    #[Test]
    public function itProducesValidQuoteAndDiv(): void
    {
        $this->assertValidPortableText(text([
            quote('quoted'),
            div('div'),
        ])->toArray());
    }

    #[Test]
    public function itProducesValidDecorators(): void
    {
        $this->assertValidPortableText(text([
            block(node('strong')->marks([Mark::Strong])),
            block(node('em')->marks([Mark::Emphasis])),
            block(node('strike')->marks([Mark::Strikethrough])),
            block(node('underline')->marks([Mark::Underline])),
            block(node('code')->marks([Mark::Code])),
            block(node('sub')->marks([Mark::Subscript])),
            block(node('sup')->marks([Mark::Superscript])),
        ])->toArray());
    }

    #[Test]
    public function itProducesValidCombinedDecorators(): void
    {
        $this->assertValidPortableText(text([
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
        ])->toArray());
    }

    #[Test]
    public function itProducesValidLinks(): void
    {
        $link = link('a-link', 'https://example.com', '_blank');
        $content = text([
            block([
                node('Hello, '),
                node('World!')->mark($link),
            ]),
        ])->toArray();

        $this->assertValidPortableText($content);

        $block = $content[0];
        $this->assertSame('link', $block['markDefs'][0]['_type']);
        $this->assertSame('https://example.com', $block['markDefs'][0]['href']);
        $this->assertSame('_blank', $block['markDefs'][0]['target']);
        $this->assertSame('a-link', $block['markDefs'][0]['_key']);
        $this->assertContains('a-link', $block['children'][1]['marks']);
        $this->assertSame([], $block['children'][0]['marks']);
    }

    #[Test]
    public function itProducesValidBulletLists(): void
    {
        $this->assertValidPortableText(text([
            bullet('First item'),
            bullet('Nested item', 2),
        ])->toArray());
    }

    #[Test]
    public function itProducesValidNumberedLists(): void
    {
        $this->assertValidPortableText(text([
            numberBullet('First item'),
            numberBullet('Nested item', 2),
        ])->toArray());
    }

    #[Test]
    public function itProducesValidMixedDocument(): void
    {
        $link = link('doc-link', 'https://example.com');

        $this->assertValidPortableText(text([
            heading1('Title'),
            'Introduction paragraph.',
            bullet('First list item'),
            bullet('Nested list item', 2),
            numberBullet('Numbered item'),
            block([
                node('Bold')->marks([Mark::Strong]),
                ' and ',
                node('linked')->mark($link),
            ]),
            quote('A quoted passage.'),
        ])->toArray());
    }

    #[Test]
    public function itProducesValidJsonSerializableOutput(): void
    {
        $text = text([
            heading2('Heading'),
            node('Hello, World!')->marks([Mark::Emphasis]),
        ]);

        $encoded = json_encode($text);

        $this->assertNotFalse($encoded);

        $decoded = json_decode($encoded, true);

        $this->assertIsArray($decoded);
        $this->assertValidPortableText($decoded);
    }
}
