<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks;

use JsonSerializable;
use Vibe\PortableText\Blocks\Lists\ListTrait;
use Vibe\PortableText\Blocks\MarkDefs\MarkDefsTrait;
use Vibe\PortableText\Blocks\Styles\Style;
use Vibe\PortableText\Blocks\Styles\StyleTrait;
use Vibe\PortableText\Blocks\Types\Type;
use Vibe\PortableText\Blocks\Types\TypeTrait;
use Vibe\PortableText\CustomAttributes\CustomAttributesTrait;
use Vibe\PortableText\Keys\KeyTrait;
use Vibe\PortableText\Nodes\Node;

class Block implements JsonSerializable
{
    use KeyTrait {
        regenerateKey as baseRegenerateKey;
    }
    use TypeTrait;
    use StyleTrait;
    use ListTrait;
    use CustomAttributesTrait;
    use MarkDefsTrait;

    /** @var list<Node> */
    protected(set) array $children = [];

    public function __construct(
        array|Node|string $children = []
    ) {
        $children = is_array($children) ? $children : [$children];

        foreach ($children as $child) {
            $this->append($child);
        }
    }

    public function regenerateKey(string $prefix, int $siblingsCount): void
    {
        $this->baseRegenerateKey($prefix, $siblingsCount);

        $siblingsCount = 0;

        foreach ($this->children as $node) {
            $node->regenerateKey($this->key, $siblingsCount++);
        }
    }

    public function parseNode(Node|string $node): Node
    {
        $node = is_string($node) ? new Node($node) : $node;

        $node->regenerateKey($this->key, count($this->children));

        foreach ($node->markDefs as $markDef) {
            $this->markDef($markDef);
        }

        $node->clearMarkDefs();

        return $node;
    }

    public function append(Node|string $node): self
    {
        $this->children[] = $this->parseNode($node);

        return $this;
    }

    public function prepend(Node|string $node): self
    {
        array_unshift($this->children, $this->parseNode($node));

        return $this;
    }

    public function toArray(): array
    {
        $arr['_type'] = $this->type instanceof Type ? $this->type->value : $this->type;

        if ($this->key !== '') {
            $arr['_key'] = $this->key;
        }

        $arr['style']    = $this->style instanceof Style ? $this->style->value : $this->style;
        $arr['markDefs'] = [];
        $arr['children'] = [];

        foreach ($this->markDefs as $markDef) {
            $arr['markDefs'][] = $markDef->toArray();
        }

        foreach ($this->children as $child) {
            $arr['children'][] = $child->toArray();
        }

        if ($this->listItem) {
            $arr['listItem'] = $this->listItem;
        }

        if ($this->level) {
            $arr['level'] = $this->level;
        }

        foreach ($this->customAttributes as $key => $value) {
            $arr[$key] = $value;
        }

        return $arr;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}