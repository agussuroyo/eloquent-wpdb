<?php

namespace Agussuroyo\EloquentWpdb;

use Illuminate\Database\Capsule\Manager;

class DB
{
    /**
     * @param callable|null $callable
     * @return Manager
     * @throws \Exception
     */
    public function capsule($callable = null)
    {
        // I'm not really sure this check is required or not, but the constants is used on the addConnection
        $constants = [
            'DB_HOST',
            'DB_NAME',
            'DB_USER',
            'DB_PASSWORD',
            'DB_CHARSET'
        ];
        foreach ($constants as $constant) {
            if (!defined($constant)) {
                throw new \Exception("No {$constant} defined.");
            }
        }

        // We need to check $wpdb is exist or not, because we require it inside the Eloquent
        global $wpdb;
        if ($wpdb === null) {
            throw new \Exception('$wpdb is required.');
        }

        // Need to make custom connection called `wpdb` to make this work
        $capsule = new Manager();
        $capsule->getDatabaseManager()->extend('wpdb', function ($config) {
            return new Connection(function () {
                return null;
            }, $config['database'], $config['prefix'], $config);
        });
        $capsule->addConnection([
            'driver' => 'wpdb',
            'host' => DB_HOST, // @phpstan-ignore-line
            'database' => DB_NAME, // @phpstan-ignore-line
            'username' => DB_USER, // @phpstan-ignore-line
            'password' => DB_PASSWORD, // @phpstan-ignore-line
            'charset' => DB_CHARSET, // @phpstan-ignore-line
            'prefix' => ''
        ]);
        if ($callable !== null) {
            $callable($capsule);
        }
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        // TODO: maybe need event listener?

        return $capsule;
    }
}
