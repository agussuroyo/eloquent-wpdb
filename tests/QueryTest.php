<?php

class QueryTest extends \PHPUnit\Framework\TestCase
{
    public $manager;
    public $db;

    protected function setUp(): void
    {
        parent::setUp();

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
        $this->manager = $db->capsule();
        $this->db = $this->manager->getConnection();
    }

    public function testBuildQuerySelect()
    {
        $this->assertEquals('select * from `tableName`', $this->db->table('tableName')->toSql());
    }

    public function testBuildQueryWhere()
    {
        $this->assertEquals(
            'select * from `tableName` where `field` = ?',
            $this->db->table('tableName')->where('field', 123)->toSql()
        );

        $this->assertEquals(
            [456],
            $this->db->table('tableName')->where('field', 456)->getBindings()
        );
    }

    public function testGetResults()
    {
        $this->assertEmpty($this->db->table('tableName')->get());
    }

}