<?php

declare(strict_types=1);

namespace Vibe\PortableText\Nodes\Types;

trait TypeTrait
{
    protected(set) Type|string $type = Type::Span;

    public function type(Type|string $type): self
    {
        $this->type = $type;

        return $this;
    }
}