<?php

namespace Saber13812002\Laravel\Fulltext\Tests;

use Saber13812002\Laravel\Fulltext\IndexedRecord;
use Saber13812002\Laravel\Fulltext\Tests\Fixtures\IndexableTestModel;

class IndexableTest extends AbstractTestCase
{
    public function testIndexedRecordReceivesUpdateIndex()
    {
        $indexedRecord = \Mockery::mock(IndexedRecord::class);
        $indexedRecord->shouldReceive('updateIndex');

        $model = new IndexableTestModel();
        $model->indexedRecord = $indexedRecord;
        $model->indexRecord();
    }

    public function testIndexedRecordReceivesDelete()
    {
        $indexedRecord = \Mockery::mock(IndexedRecord::class);
        $indexedRecord->shouldReceive('delete');

        $model = new IndexableTestModel();
        $model->indexedRecord = $indexedRecord;
        $model->unIndexRecord();
    }
}
