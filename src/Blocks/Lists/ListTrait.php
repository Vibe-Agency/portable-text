<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\Lists;

trait ListTrait
{
    protected(set) ListItem|string|null $listItem = null;

    protected(set) int|null $level = null;

    public function listItem(ListItem|string $listItem): self
    {
        $this->listItem = $listItem;

        return $this;
    }

    public function level(int $level): self
    {
        $this->level = $level;

        return $this;
    }
}