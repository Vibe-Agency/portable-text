<?php

declare(strict_types=1);

namespace Vibe\PortableText\Nodes;

use JsonSerializable;
use Vibe\PortableText\CustomAttributes\CustomAttributesTrait;
use Vibe\PortableText\Keys\KeyTrait;
use Vibe\PortableText\Nodes\Marks\Mark;
use Vibe\PortableText\Nodes\Marks\MarkTrait;
use Vibe\PortableText\Nodes\Text\TextTrait;
use Vibe\PortableText\Nodes\Types\Type;
use Vibe\PortableText\Nodes\Types\TypeTrait;

class Node implements JsonSerializable
{
    use KeyTrait;
    use TypeTrait;
    use TextTrait;
    use CustomAttributesTrait;
    use MarkTrait;

    public function __construct(string $text)
    {
        $this->text($text);
    }

    public function toArray(): array
    {
        $arr['_type'] = $this->type instanceof Type ? $this->type->value : $this->type;

        if ($this->key !== '') {
            $arr['_key'] = $this->key;
        }

        $arr['text']  = $this->text;
        $arr['marks'] = [];

        foreach ($this->marks as $mark) {
            $arr['marks'][] = $mark instanceof Mark ? $mark->value : $mark;
        }

        foreach ($this->customAttributes as $key => $value) {
            $arr[$key] = $value;
        }

        return $arr;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}