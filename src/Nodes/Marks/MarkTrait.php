<?php

declare(strict_types=1);

namespace Vibe\PortableText\Nodes\Marks;

use Vibe\PortableText\Blocks\MarkDefs\MarkDef;

trait MarkTrait
{
    /**
     * @var list<Mark|string>
     */
    protected(set) array $marks = [];

    /**
     * @var list<MarkDef>
     */
    protected(set) array $markDefs = [];

    /**
     * @param  list<MarkDef|Mark|string>  $marks
     */
    public function marks(array $marks): self
    {
        foreach ($marks as $mark) {
            $this->mark($mark);
        }

        return $this;
    }

    public function mark(MarkDef|Mark|string $mark): self
    {
        if ($mark instanceof MarkDef) {
            $this->markDefs[] = $mark;
        }

        $this->marks[] = $mark instanceof MarkDef ? $mark->key : $mark;

        return $this;
    }

    public function clearMarkDefs(): self
    {
        $this->markDefs = [];

        return $this;
    }
}