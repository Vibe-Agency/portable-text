<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\MarkDefs\Types;

trait TypeTrait
{
    protected(set) Type|string $type;

    public function type(Type|string $type): self
    {
        $this->type = $type;

        return $this;
    }
}