<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\Types;

trait TypeTrait
{
    protected(set) Type|string $type = Type::Block;

    public function type(Type|string $type): self
    {
        $this->type = $type;

        return $this;
    }
}