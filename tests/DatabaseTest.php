<?php

use \PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    public function testRun()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No DB_HOST defined.');

        $db = new \Agussuroyo\EloquentWpdb\DB();
        $db->capsule();
    }

    public function testSimulateConstants()
    {
        $this->expectExceptionMessage('$wpdb is required.');

        $constants = [
            'DB_HOST',
            'DB_NAME',
            'DB_USER',
            'DB_PASSWORD',
            'DB_CHARSET'
        ];
        foreach ($constants as $constant) {
            defined($constant) || define($constant, $constant);
        }

        $db = new \Agussuroyo\EloquentWpdb\DB();
        $db->capsule();
    }

    public function testSimulateConstantsAndWpdb()
    {
        $constants = [
            'DB_HOST',
            'DB_NAME',
            'DB_USER',
            'DB_PASSWORD',
            'DB_CHARSET'
        ];
        foreach ($constants as $constant) {
            defined($constant) || define($constant, $constant);
        }

        $GLOBALS['wpdb'] = new \Agussuroyo\EloquentWpdb\FakeWpdb();

        $db = new \Agussuroyo\EloquentWpdb\DB();
        $manager = $db->capsule();
        $this->assertInstanceOf(\Illuminate\Database\Capsule\Manager::class, $manager);

        $connection = $manager->getConnection();
        $this->assertInstanceOf(\Illuminate\Database\Connection::class, $connection);
        $this->assertInstanceOf(\Agussuroyo\EloquentWpdb\Connection::class, $connection);
    }
}