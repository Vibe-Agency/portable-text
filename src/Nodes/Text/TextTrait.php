<?php

declare(strict_types=1);

namespace Vibe\PortableText\Nodes\Text;

trait TextTrait
{
    protected(set) string $text = '';

    public function text(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}