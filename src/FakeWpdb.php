<?php

namespace Agussuroyo\EloquentWpdb;

class FakeWpdb
{
    /**
     * @param string $query
     * @param string $type
     * @return array
     */
    public function get_results($query, $type)
    {
        return [];
    }
}
