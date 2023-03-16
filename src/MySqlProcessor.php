<?php

namespace Agussuroyo\EloquentWpdb;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Processors\MySqlProcessor as Processor;

class MySqlProcessor extends Processor
{
    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null)
    {
        $query->getConnection()->insert($sql, $values);
        global $wpdb;
        $id = $wpdb->insert_id;
        return is_numeric($id) ? (int) $id : $id;
    }
}
