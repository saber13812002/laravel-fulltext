<?php

namespace Saber13812002\Laravel\Fulltext\Tests;

use function PHPUnit\Framework\assertEquals;

class NormalizedTest
{
    public function testNormalizer()
    {
        assertEquals(IndexedRecord->normalize("الْعَالَمِينَ"),"العالمين");
    }
}
