<?php

namespace Agussuroyo\EloquentWpdb;

class Connector extends \Illuminate\Database\Connectors\Connector
{
    /**
     * @param array $config
     * @return \mysqli|resource|false|null
     */
    public function connect(array $config)
    {
        global $wpdb;
        return $wpdb->dbh;
    }
}
