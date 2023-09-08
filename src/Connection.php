<?php

namespace Agussuroyo\EloquentWpdb;

use Illuminate\Database\MySqlConnection;

class Connection extends MySqlConnection
{
    /**
     * @param string $query
     * @param array $bindings
     * @return string|void
     */
    public function wpPrepare($query, $bindings = [])
    {
        if (empty($bindings)) {
            return $query;
        }

        // Replace `?` with `%s`, `%d`, or `%f`
        $replacer = $this->wpBindingsToType($bindings);
        $query = preg_replace_callback('/\?/', static function () use (&$replacer) {
            return array_shift($replacer);
        }, $query);

        global $wpdb;
        return $wpdb->prepare($query, $bindings);
    }

    /**
     * @param string $query
     * @param array $bindings
     * @return int|bool
     */
    protected function wpQueryPrepare($query, $bindings = [])
    {
        global $wpdb;
        return $wpdb->query($this->wpPrepare($query, $bindings));
    }

    /**
     * @param array $bindings
     * @return array
     */
    public function wpBindingsToType(&$bindings)
    {
        $types = [];
        foreach ($bindings as $key => $binding) {
            switch (gettype($binding)) {
                case 'boolean':
                    $types[] = '%f';
                    break;
                case 'integer':
                case 'double':
                case 'float':
                    $types[] = '%d';
                    break;
                // handle null & need to remove binding
                case 'NULL':
                    $types[] = 'NULL';
                    unset($bindings[$key]);
                    break;
                default:
                    $types[] = '%s';
                    break;
            }
        }
        return $types;
    }

    public function select($query, $bindings = [], $useReadPdo = true)
    {
        return (array) $this->run($query, $bindings, function ($query, $bindings) {
            if ($this->pretending()) {
                return [];
            }
            global $wpdb;
            return $wpdb->get_results($this->wpPrepare($query, $bindings), 'ARRAY_A');
        });
    }

    public function statement($query, $bindings = [])
    {
        return (bool) $this->run($query, $bindings, function ($query, $bindings) {
            if ($this->pretending()) {
                return true;
            }
            $execute = $this->wpQueryPrepare($query, $bindings);
            $this->recordsHaveBeenModified();
            return (bool) $execute;
        });
    }

    public function affectingStatement($query, $bindings = [])
    {
        $affectedRows = $this->run($query, $bindings, function ($query, $bindings) {
            if ($this->pretending()) {
                return 0;
            }
            $execute = $this->wpQueryPrepare($query, $bindings);
            $affectedRows = (int) $execute;
            $this->recordsHaveBeenModified($affectedRows > 0);
            return $affectedRows;
        });

        /** @phpstan-ignore-next-line */
        return (int) $affectedRows;
    }

    public function unprepared($query)
    {
        return (bool) $this->run($query, [], function ($query) {
            if ($this->pretending()) {
                return true;
            }
            global $wpdb;
            $this->recordsHaveBeenModified(
                $change = $wpdb->query($query) !== false
            );
            return $change;
        });
    }

    public function bindValues($statement, $bindings)
    {
        // Must be doing nothing in $wpdb
    }

    public function reconnect()
    {
        global $wpdb;
        $wpdb->db_connect();
    }

    public function getPdo()
    {
        return $this->getRawPdo();
    }

    /**
     * @return \PDO
     * @throws \Exception
     */
    public function getRawPdo()
    {
        throw new \Exception('We\'re not using PDO connection here.');
    }

    /**
     * @return \Doctrine\DBAL\Connection
     * @throws \Exception
     *
     * @phpstan-ignore-next-line
     */
    public function getDoctrineConnection()
    {
        throw new \Exception('We\'re not using doctrine connection here.');
    }

    public function logQuery($query, $bindings, $time = null)
    {
        parent::logQuery($query, $bindings, $time);

        if (!function_exists('do_action')) {
            return;
        }

        do_action('qm/info', [
            'query' => $query,
            'bindings' => $bindings,
            'time' => $time
        ]);
    }

    protected function getDefaultPostProcessor()
    {
        return new MySqlProcessor();
    }
}
