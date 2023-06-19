<?php

namespace Saber13812002\Laravel\Fulltext\Tests;

use Saber13812002\Laravel\Fulltext\Indexer;
use Saber13812002\Laravel\Fulltext\Tests\Fixtures\TestModel;

class IndexerTest extends AbstractTestCase
{
    public function testIndexModel()
    {
        $indexer = new Indexer();
        $model = \Mockery::mock(TestModel::class);
        $model->shouldReceive('indexRecord');
        $indexer->indexModel($model);
    }
}
