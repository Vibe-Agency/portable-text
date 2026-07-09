<?php

declare(strict_types=1);

namespace Tests\Support;

use Vibe\PortableText\Nodes\Marks\Mark;

class PortableTextValidator
{
    /** @var list<string> */
    private array $violations = [];

    /** @var list<string> */
    private const DECORATORS = [
        Mark::Strong->value,
        Mark::Emphasis->value,
        Mark::Strikethrough->value,
        Mark::Underline->value,
        Mark::Code->value,
        Mark::Subscript->value,
        Mark::Superscript->value,
    ];

    /**
     * @return list<string>
     */
    public function validate(mixed $content): array
    {
        $this->violations = [];

        if (! is_array($content) || ! array_is_list($content)) {
            return ['Root must be a list of content objects.'];
        }

        foreach ($content as $index => $item) {
            $this->validateItem($item, "[$index]");
        }

        if ($this->violations === [] && json_encode($content) === false) {
            $this->violations[] = 'Root content must be JSON-serializable.';
        }

        return $this->violations;
    }

    private function validateItem(mixed $item, string $path): void
    {
        if (! is_array($item)) {
            $this->violations[] = "$path must be an object.";

            return;
        }

        if (! isset($item['_type']) || ! is_string($item['_type']) || $item['_type'] === '') {
            $this->violations[] = "$path._type must be a non-empty string.";

            return;
        }

        if ($item['_type'] === 'block') {
            $this->validateBlock($item, $path);
        }
    }

    /**
     * @param  array<string, mixed>  $block
     */
    private function validateBlock(array $block, string $path): void
    {
        if (! isset($block['_key']) || ! is_string($block['_key']) || $block['_key'] === '') {
            $this->violations[] = "$path._key must be a non-empty string.";
        }

        if (! isset($block['style']) || ! is_string($block['style'])) {
            $this->violations[] = "$path.style must be a string.";
        }

        if (! isset($block['children']) || ! is_array($block['children']) || ! array_is_list($block['children'])) {
            $this->violations[] = "$path.children must be a list.";

            return;
        }

        $markDefKeys = [];

        if (isset($block['markDefs'])) {
            if (! is_array($block['markDefs']) || ! array_is_list($block['markDefs'])) {
                $this->violations[] = "$path.markDefs must be a list.";
            } else {
                foreach ($block['markDefs'] as $index => $markDef) {
                    $markDefPath = "$path.markDefs[$index]";
                    $key = $this->validateMarkDef($markDef, $markDefPath);

                    if ($key !== null) {
                        $markDefKeys[] = $key;
                    }
                }
            }
        }

        if (isset($block['listItem']) && ! is_string($block['listItem'])) {
            $this->violations[] = "$path.listItem must be a string.";
        }

        if (isset($block['level']) && (! is_int($block['level']) || $block['level'] < 1)) {
            $this->violations[] = "$path.level must be a positive integer.";
        }

        foreach ($block['children'] as $index => $child) {
            $this->validateChild($child, "$path.children[$index]", $markDefKeys);
        }
    }

    /**
     * @return string|null
     */
    private function validateMarkDef(mixed $markDef, string $path): ?string
    {
        if (! is_array($markDef)) {
            $this->violations[] = "$path must be an object.";

            return null;
        }

        if (! isset($markDef['_type']) || ! is_string($markDef['_type']) || $markDef['_type'] === '') {
            $this->violations[] = "$path._type must be a non-empty string.";
        }

        if (! isset($markDef['_key']) || ! is_string($markDef['_key']) || $markDef['_key'] === '') {
            $this->violations[] = "$path._key must be a non-empty string.";

            return null;
        }

        return $markDef['_key'];
    }

    /**
     * @param  list<string>  $markDefKeys
     */
    private function validateChild(mixed $child, string $path, array $markDefKeys): void
    {
        if (! is_array($child)) {
            $this->violations[] = "$path must be an object.";

            return;
        }

        if (! isset($child['_type']) || ! is_string($child['_type']) || $child['_type'] === '') {
            $this->violations[] = "$path._type must be a non-empty string.";

            return;
        }

        if ($child['_type'] === 'span') {
            $this->validateSpan($child, $path, $markDefKeys);
        }
    }

    /**
     * @param  array<string, mixed>  $span
     * @param  list<string>  $markDefKeys
     */
    private function validateSpan(array $span, string $path, array $markDefKeys): void
    {
        if (! isset($span['_key']) || ! is_string($span['_key']) || $span['_key'] === '') {
            $this->violations[] = "$path._key must be a non-empty string.";
        }

        if (! isset($span['text']) || ! is_string($span['text'])) {
            $this->violations[] = "$path.text must be a string.";
        }

        if (! isset($span['marks']) || ! is_array($span['marks']) || ! array_is_list($span['marks'])) {
            $this->violations[] = "$path.marks must be a list.";

            return;
        }

        foreach ($span['marks'] as $index => $mark) {
            if (! is_string($mark)) {
                $this->violations[] = "$path.marks[$index] must be a string.";

                continue;
            }

            if (! in_array($mark, self::DECORATORS, true) && ! in_array($mark, $markDefKeys, true)) {
                $this->violations[] = "$path.marks[$index] references unknown annotation key \"$mark\".";
            }
        }
    }
}
