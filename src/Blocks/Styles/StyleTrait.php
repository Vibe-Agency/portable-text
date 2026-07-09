<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\Styles;

trait StyleTrait
{
    protected(set) Style|string $style = Style::Normal;

    public function style(Style|string $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function normal(): self
    {
        return $this->style(Style::Normal);
    }

    public function heading1(): self
    {
        return $this->style(Style::Heading1);
    }

    public function heading2(): self
    {
        return $this->style(Style::Heading2);
    }

    public function heading3(): self
    {
        return $this->style(Style::Heading3);
    }

    public function heading4(): self
    {
        return $this->style(Style::Heading4);
    }

    public function heading5(): self
    {
        return $this->style(Style::Heading5);
    }

    public function heading6(): self
    {
        return $this->style(Style::Heading6);
    }

    public function quote(): self
    {
        return $this->style(Style::Quote);
    }

    public function div(): self
    {
        return $this->style(Style::Div);
    }
}