<?php

declare(strict_types=1);

namespace Vibe\PortableText\Keys;

trait KeyTrait
{
    protected(set) string $key = '';

    protected function generateKey(string $prefix, int $siblingsCount): string
    {
        if ($prefix === '') {
            return (string) $siblingsCount;
        }

        return $prefix.'-'.$siblingsCount;
    }

    public function regenerateKey(string $prefix, int $siblingsCount): void
    {
        $this->key = $this->generateKey($prefix, $siblingsCount);
    }
}