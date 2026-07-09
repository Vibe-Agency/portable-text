<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\MarkDefs;

trait MarkDefsTrait
{
    /**
     * @var list<MarkDef>
     */
    protected(set) array $markDefs = [];

    public function markDefs(array $markDefs): self
    {
        $this->markDefs = [];

        foreach ($markDefs as $markDef) {
            $this->markDef($markDef);
        }

        return $this;
    }

    public function markDef(MarkDef $markDef): self
    {
        $this->markDefs[$markDef->key] = $markDef;

        return $this;
    }
}