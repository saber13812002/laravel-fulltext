<?php

namespace Saber13812002\Laravel\Fulltext\Tests\Fixtures;

use Saber13812002\Laravel\Fulltext\Indexable;

class IndexableTestModel extends TestModel
{
    use Indexable;

    public $indexRecord;
}
