<?php

declare(strict_types=1);

namespace Tests\Support;

trait AssertsPortableText
{
    protected function assertValidPortableText(array $content): void
    {
        $violations = new PortableTextValidator()->validate($content);

        $this->assertSame([], $violations, implode("\n", $violations));
    }
}
