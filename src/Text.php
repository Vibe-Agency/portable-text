<?php

declare(strict_types=1);

namespace Vibe\PortableText;

use JsonSerializable;
use Vibe\PortableText\Blocks\Block;
use Vibe\PortableText\Nodes\Node;
use function Vibe\PortableText\Blocks\block;
use function Vibe\PortableText\Nodes\node;

class Text implements JsonSerializable
{
    /** @var list<Block> */
    protected(set) array $blocks = [];

    /**
     * @param  list<Block|Node|string|null>|Block|Node|string  $blocks
     */
    public function __construct(
        array|Block|Node|string $blocks = [],
        protected(set) readonly string $key = 'text',
    ) {
        $blocks = is_array($blocks) ? $blocks : [$blocks];

        foreach ($blocks as $block) {
            if ($block === null) {
                continue;
            }

            $this->append($block);
        }
    }

    private function parseBlock(Block|Node|string $block): Block
    {
        if (is_string($block)) {
            $block = node($block);
        }

        if ($block instanceof Node) {
            $block = block([$block]);
        }

        $block->regenerateKey($this->key, count($this->blocks));

        return $block;
    }

    public function append(Block|Node|string $block): self
    {
        $this->blocks[] = $this->parseBlock($block);

        return $this;
    }

    public function prepend(Block|Node|string $block): self
    {
        array_unshift($this->blocks, $this->parseBlock($block));

        return $this;
    }

    public function toArray(): array
    {
        $blocks = [];

        foreach ($this->blocks as $block) {
            $blocks[] = $block->toArray();
        }

        return $blocks;
    }

    public function jsonSerialize(): array
    {
       return $this->toArray();
    }
}