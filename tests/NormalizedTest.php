<?php

namespace Saber13812002\Laravel\Fulltext\Tests;

use Saber13812002\Laravel\Fulltext\IndexedRecord;
use function PHPUnit\Framework\assertEquals;

class NormalizedTest
{
    public function testNormalizer(): void
    {
        assertEquals(IndexedRecord::normalize("الْعَالَمِينَ"),"العالمين");
    }
}
