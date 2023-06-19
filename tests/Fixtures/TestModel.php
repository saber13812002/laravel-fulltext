<?php

namespace Saber13812002\Laravel\Fulltext\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class TestModel extends Model
{
    public $id = 1;

    public function searchableAs()
    {
        return 'table';
    }

    public function getKey()
    {
        return $this->id;
    }

    public function toSearchableArray()
    {
        return ['id' => 1];
    }
}
