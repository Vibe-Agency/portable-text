# Portable Text for PHP

A PHP 8.5 library for building [Portable Text](https://github.com/portabletext/portabletext) JSON programmatically. It produces Sanity-compatible rich text arrays ready for CMS storage or API responses.

This package is a **builder only** — it writes Portable Text content. It does not parse existing Portable Text or render it to HTML. Use a renderer such as [Sanity PHP](https://github.com/sanity-io/sanity-php) on the consumer side.

## Requirements

- PHP 8.5
- No runtime dependencies

## Installation

```bash
composer config repositories.vibe/portable-text vcs git@github.com:Vibe-Agency/portable-text.git
composer require vibe/portable-text
```

## Quick start

```php
use function Vibe\PortableText\text;

$document = text('Hello, World!');

$portableText = $document->toArray();
$json = json_encode($document);
```

`toArray()` returns a root array of block objects:

```json
[
  {
    "_type": "block",
    "_key": "text-0",
    "style": "normal",
    "markDefs": [],
    "children": [
      {
        "_type": "span",
        "_key": "text-0-0",
        "text": "Hello, World!",
        "marks": []
      }
    ]
  }
]
```

## Core concepts

Portable Text is a recursive array structure. This library models it with four building blocks:

| Class | Role | Serialized `_type` |
|-------|------|----------------|
| `Text` | Root document; holds an ordered list of blocks | *(root array)* |
| `Block` | A section of text — paragraph, heading, list item, or custom block object | `block` (default), custom |
| `Node` | An inline text span, or custom inline object | `span` (default), custom |
| `MarkDef` | An annotation definition referenced by span marks (e.g. a link) | `link`, `comment`, custom |

```
Text
 └── Block
      ├── Node (span)
      │    └── MarkDef (annotation, promoted to block)
      └── MarkDef (annotation)
```

### Auto-wrapping

When you pass content to `text()`, strings and `Node` instances are automatically wrapped into blocks:

- `'Hello'` → `block([node('Hello')])`
- `node('Hello')` → `block([node('Hello')])`
- `block([...])` → used as-is

Each top-level item in `text([...])` becomes its own block. To place multiple spans in a single block (for example, a partially linked sentence), use `block([...])` explicitly.

### Mark-def promotion

When a `Node` is annotated with `mark($markDef)`, the mark definition is hoisted to the parent block's `markDefs` array. The span's `marks` array holds the mark-def key string, not the full definition.

```php
use function Vibe\PortableText\Blocks\block;
use function Vibe\PortableText\Blocks\MarkDefs\Types\link;
use function Vibe\PortableText\Nodes\node;
use function Vibe\PortableText\text;

$link = link('my-link', 'https://example.com', '_blank');

$document = text([
    block([
        node('Click '),
        node('here')->mark($link),
    ]),
]);
```

```json
[
  {
    "_type": "block",
    "_key": "text-0",
    "style": "normal",
    "markDefs": [
      {
        "_type": "link",
        "_key": "my-link",
        "href": "https://example.com",
        "target": "_blank"
      }
    ],
    "children": [
      { "_type": "span", "_key": "text-0-0", "text": "Click ", "marks": [] },
      { "_type": "span", "_key": "text-0-1", "text": "here", "marks": ["my-link"] }
    ]
  }
]
```

### Key generation

Blocks and spans receive auto-generated `_key` values based on their position in the document:

| Element | Key pattern | Example |
|---------|-------------|---------|
| First block | `{prefix}-{index}` | `text-0` |
| First span in block | `{blockKey}-{index}` | `text-0-0` |
| Second block | `{prefix}-{index}` | `text-1` |

The default prefix is `text` (configurable via `text($blocks, $key)`). Mark-def keys are **user-supplied** in the constructor and are not auto-generated.

## Building content

### Plain text and paragraphs

```php
use function Vibe\PortableText\text;

// Single paragraph
text('A plain paragraph.');

// Multiple paragraphs
text([
    'First paragraph.',
    'Second paragraph.',
]);
```

### Explicit blocks

```php
use function Vibe\PortableText\Blocks\blankLine;
use function Vibe\PortableText\Blocks\block;
use function Vibe\PortableText\Nodes\node;
use function Vibe\PortableText\text;

text([
    block([
        node('First span'),
        ' and ',
        node('second span'),
    ]),
    blankLine(),
]);
```

`blankLine()` creates an empty block, useful for intentional vertical spacing.

### Headings

Style helpers create blocks with the appropriate heading style:

```php
use function Vibe\PortableText\Blocks\Styles\heading1;
use function Vibe\PortableText\Blocks\Styles\heading2;
use function Vibe\PortableText\text;

text([
    heading1('Page title'),
    heading2('Section heading'),
]);
```

Equivalent fluent form:

```php
use function Vibe\PortableText\Blocks\block;

block('Heading text')->heading3();
```

Available heading helpers: `heading1()` through `heading6()`.

### Block styles

| Helper / method | Output `style` |
|-----------------|----------------|
| `normal()` | `normal` |
| `heading1()` – `heading6()` | `h1` – `h6` |
| `quote()` | `blockquote` |
| `div()` | `div` |

```php
use function Vibe\PortableText\Blocks\Styles\div;
use function Vibe\PortableText\Blocks\Styles\quote;
use function Vibe\PortableText\text;

text([
    quote('A quoted passage.'),
    div('A div-styled block.'),
]);
```

### Inline decorators

Decorators are simple string marks applied directly to spans. Use the `Mark` enum or raw strings:

```php
use Vibe\PortableText\Nodes\Marks\Mark;
use function Vibe\PortableText\Blocks\block;
use function Vibe\PortableText\Nodes\node;
use function Vibe\PortableText\text;

text([
    block(node('Important')->marks([Mark::Strong, Mark::Emphasis])),
]);
```

| Enum case | Value |
|-----------|-------|
| `Mark::Strong` | `strong` |
| `Mark::Emphasis` | `em` |
| `Mark::Strikethrough` | `strike-through` |
| `Mark::Underline` | `underline` |
| `Mark::Code` | `code` |
| `Mark::Subscript` | `subscript` |
| `Mark::Superscript` | `superscript` |

Multiple decorators can be combined on a single span:

```php
node('text')->marks([Mark::Emphasis, Mark::Strong]);
```

### Links

Use the `link()` helper to create a link mark definition:

```php
use function Vibe\PortableText\Blocks\MarkDefs\Types\link;
use function Vibe\PortableText\Nodes\node;

$link = link('my-link', 'https://example.com', '_blank');

node('link text')->mark($link);
```

Parameters: `link(string $key, string $href, string $target = '_self')`.

### Lists

```php
use function Vibe\PortableText\Blocks\Lists\bullet;
use function Vibe\PortableText\Blocks\Lists\numberBullet;
use function Vibe\PortableText\text;

text([
    bullet('Top-level item'),
    bullet('Nested item', 2),
    numberBullet('Numbered item'),
    numberBullet('Nested numbered item', 2),
]);
```

List blocks include `listItem` (`"bullet"` or `"number"`) and `level` (nesting depth, default `1`).

### Custom attributes

Add arbitrary data to blocks, spans, and mark definitions:

```php
use Vibe\PortableText\Blocks\MarkDefs\Types\Type;
use function Vibe\PortableText\Blocks\block;
use function Vibe\PortableText\Blocks\MarkDefs\markDef;
use function Vibe\PortableText\Nodes\node;

block('Content')
    ->customAttribute('className', 'lead');

node('text')
    ->customAttribute('data-id', '42');

markDef('comment-1', Type::Comment)
    ->customAttribute('text', 'Editor note')
    ->customAttribute('author', 'user-123');
```

Custom attributes are merged as top-level keys in the serialized output.

### Custom block and inline types

Portable Text supports custom content types beyond the built-in `block` and `span` defaults. The easiest way to configure them is with `type()` on `Block` and `Node` — no subclassing required.

Use `type()` with any string to set the serialized `_type` value. Combine with `customAttribute()` to attach the data your schema expects:

```php
use function Vibe\PortableText\Blocks\block;
use function Vibe\PortableText\Nodes\node;
use function Vibe\PortableText\text;

$document = text([
    // Custom block object (e.g. an image embed)
    block()
        ->type('image')
        ->customAttribute('asset', ['_ref' => 'image-123']),

    // Custom inline object inside a text block
    block([
        node('AAPL')
            ->type('stock-ticker')
            ->customAttribute('symbol', 'AAPL'),
    ]),
]);
```

```json
[
  {
    "_type": "image",
    "_key": "text-0",
    "style": "normal",
    "markDefs": [],
    "children": [],
    "asset": { "_ref": "image-123" }
  },
  {
    "_type": "block",
    "_key": "text-1",
    "style": "normal",
    "markDefs": [],
    "children": [
      {
        "_type": "stock-ticker",
        "_key": "text-1-0",
        "text": "AAPL",
        "marks": [],
        "symbol": "AAPL"
      }
    ]
  }
]
```

The built-in type enums (`Blocks\Types\Type::Block`, `Nodes\Types\Type::Span`) are defaults only. Passing a custom string overrides them:

```php
block('Paragraph')->type('block');   // default, equivalent to omitting type()
node('text')->type('span');          // default, equivalent to omitting type()
block()->type('code')->customAttribute('language', 'php');
```

Custom types work alongside all other fluent methods — styles, lists, marks, and custom attributes can be combined as needed.

### Custom mark definitions

For annotations beyond links, use `markDef()`:

```php
use function Vibe\PortableText\Blocks\MarkDefs\markDef;
use function Vibe\PortableText\Blocks\MarkDefs\Types\Type;
use function Vibe\PortableText\Nodes\node;

$comment = markDef('comment-1', Type::Comment)
    ->customAttribute('text', 'Fix typo');

node('annotated text')->mark($comment);
```

Built-in mark-def types: `link`, `comment`. Custom type strings are also supported.

### Incremental building

Build a document step by step with `append()` and `prepend()`:

```php
use function Vibe\PortableText\Blocks\Styles\heading1;
use function Vibe\PortableText\text;

$document = text()
    ->append(heading1('Title'))
    ->append('Body paragraph.');

$portableText = $document->toArray();
```

## Full document example

```php
use Vibe\PortableText\Nodes\Marks\Mark;
use function Vibe\PortableText\Blocks\block;
use function Vibe\PortableText\Blocks\Lists\bullet;
use function Vibe\PortableText\Blocks\Lists\numberBullet;
use function Vibe\PortableText\Blocks\MarkDefs\Types\link;
use function Vibe\PortableText\Blocks\Styles\heading1;
use function Vibe\PortableText\Blocks\Styles\quote;
use function Vibe\PortableText\Nodes\node;
use function Vibe\PortableText\text;

$link = link('doc-link', 'https://example.com');

$document = text([
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
]);

$portableText = $document->toArray();
$json = json_encode($document);
```

## Helper function reference

All helpers are autoloaded globally via Composer.

### `Vibe\PortableText`

| Function | Description |
|----------|-------------|
| `text(array\|Block\|Node\|string $blocks = [], string $key = 'text'): Text` | Create a root document |

### `Vibe\PortableText\Blocks`

| Function | Description |
|----------|-------------|
| `block(array\|Node\|string $children = []): Block` | Create a block |
| `blankLine(): Block` | Create an empty block |

### `Vibe\PortableText\Blocks\Styles`

| Function | Style |
|----------|-------|
| `heading1()` – `heading6()` | `h1` – `h6` |
| `quote()` | `blockquote` |
| `div()` | `div` |

Each accepts `array|Node|string $children`.

### `Vibe\PortableText\Blocks\Lists`

| Function | Description |
|----------|-------------|
| `bullet(array\|Node\|string $children = [], int $level = 1): Block` | Bullet list item |
| `numberBullet(array\|Node\|string $children = [], int $level = 1): Block` | Numbered list item |

### `Vibe\PortableText\Blocks\MarkDefs`

| Function | Description |
|----------|-------------|
| `markDef(string $key, Type\|string $type): MarkDef` | Create a mark definition |

### `Vibe\PortableText\Blocks\MarkDefs\Types`

| Function | Description |
|----------|-------------|
| `link(string $key, string $href, string $target = '_self'): MarkDef` | Create a link mark definition |

### `Vibe\PortableText\Nodes`

| Function | Description |
|----------|-------------|
| `node(string $text): Node` | Create a span node |

## Fluent API reference

### `Text`

| Method | Description |
|--------|-------------|
| `append(Block\|Node\|string $block): self` | Add a block to the end |
| `prepend(Block\|Node\|string $block): self` | Add a block to the beginning |
| `toArray(): array` | Serialize to a Portable Text array |
| `jsonSerialize(): array` | Same as `toArray()` |

### `Block`

| Method | Description |
|--------|-------------|
| `append(Node\|string $node): self` | Add a span |
| `prepend(Node\|string $node): self` | Prepend a span |
| `type(Blocks\Types\Type\|string $type): self` | Set block `_type` |
| `style(Style\|string $style): self` | Set block style |
| `normal()`, `heading1()`–`heading6()`, `quote()`, `div()` | Style shortcuts |
| `listItem(ListItem\|string $listItem): self` | Set list type |
| `level(int $level): self` | Set list nesting level |
| `markDef(MarkDef $markDef): self` | Add a mark definition |
| `customAttribute(string $name, mixed $value): self` | Add a custom attribute |
| `toArray(): array` | Serialize to a block object |

### `Node`

| Method | Description |
|--------|-------------|
| `text(string $text): self` | Set span text |
| `type(Nodes\Types\Type\|string $type): self` | Set span `_type` |
| `mark(MarkDef\|Mark\|string $mark): self` | Add a single mark |
| `marks(array $marks): self` | Add multiple marks |
| `customAttribute(string $name, mixed $value): self` | Add a custom attribute |
| `toArray(): array` | Serialize to a span object |

### `MarkDef`

| Method | Description |
|--------|-------------|
| `type(Type\|string $type): self` | Set mark-def type |
| `customAttribute(string $name, mixed $value): self` | Add a custom attribute |
| `toArray(): array` | Serialize to a mark-def object |

## Output and serialization

All core classes implement `JsonSerializable`. Both `toArray()` and `json_encode()` produce the same structure.

### Block shape

```json
{
  "_type": "block",
  "_key": "text-0",
  "style": "normal",
  "markDefs": [],
  "children": []
}
```

`_type` defaults to `"block"` but can be any custom string (e.g. `"image"`, `"code"`). Optional fields: `listItem`, `level`, and any custom attributes.

### Custom block shape

```json
{
  "_type": "image",
  "_key": "text-0",
  "asset": { "_ref": "image-123" }
}
```

Custom block objects still include builder defaults (`style`, `markDefs`, `children`) unless you configure them otherwise. Use `customAttribute()` for type-specific fields.

### Span shape

```json
{
  "_type": "span",
  "_key": "text-0-0",
  "text": "Hello",
  "marks": []
}
```

The `marks` array contains decorator strings (`"strong"`, `"em"`, etc.) and/or mark-def key strings for annotations.

### Custom inline shape

```json
{
  "_type": "stock-ticker",
  "_key": "text-0-0",
  "text": "AAPL",
  "marks": [],
  "symbol": "AAPL"
}
```

`_type` defaults to `"span"` but can be any custom string for inline objects defined in your Portable Text schema.

### Mark definition shape

```json
{
  "_type": "link",
  "_key": "my-link",
  "href": "https://example.com",
  "target": "_blank"
}
```

Extra fields (`href`, `target`, etc.) are set via `customAttribute()`.

## Rendering

This package does not render HTML. To convert output for display, pass the array to a Portable Text renderer.

With [Sanity PHP](https://github.com/sanity-io/sanity-php):

```php
use Sanity\BlockContent;

$html = BlockContent::toHtml($document->toArray());
```

Note that Sanity PHP does not render all custom attributes (for example, `target` on links). Those attributes are still present in the JSON and available to custom renderers.

## Development

```bash
composer install
./vendor/bin/phpunit
```

The test suite includes:

- **Validity tests** — assert output conforms to the Portable Text spec structure
- **Rendering tests** — smoke-test output through Sanity PHP's `BlockContent::toHtml()`

## Author

Marc Coupland — [marcus@vibeagency.uk](mailto:marcus@vibeagency.uk)
