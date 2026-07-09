<?php

declare(strict_types=1);

namespace Vibe\PortableText\Blocks\MarkDefs;

use JsonSerializable;
use Vibe\PortableText\Blocks\MarkDefs\Types\Type;
use Vibe\PortableText\Blocks\MarkDefs\Types\TypeTrait;
use Vibe\PortableText\CustomAttributes\CustomAttributesTrait;
use Vibe\PortableText\Keys\KeyTrait;

class MarkDef implements JsonSerializable
{
    use KeyTrait;
    use TypeTrait;
    use CustomAttributesTrait;

    public function __construct(string $key, Type|string $type)
    {
        $this->type = $type;
        $this->key  = $key;
    }

    public function toArray(): array
    {
        $arr['_type'] = $this->type instanceof Type ? $this->type->value : $this->type;
        $arr['_key']  = $this->key;

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