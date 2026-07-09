<?php

declare(strict_types=1);

namespace Vibe\PortableText\CustomAttributes;

trait CustomAttributesTrait
{
    /** @var array<string, mixed> */
    protected(set) array $customAttributes = [];

    public function customAttributes(array $attributes): self
    {
        $this->customAttributes = $attributes;

        return $this;
    }

    public function customAttribute(string $name, mixed $value): self
    {
        $this->customAttributes[$name] = $value;

        return $this;
    }
}