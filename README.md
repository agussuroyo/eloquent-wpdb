# $wpdb with Eloquent from Laravel

[![PHP Validator](https://github.com/jonyextenz/eloquent-wpdb/actions/workflows/php.yml/badge.svg)](https://github.com/jonyextenz/eloquent-wpdb/actions/workflows/php.yml)

- I made this just for personal use
- I do not guarantee anything for the use of this software
- Do It With Your Own Risk

# Installation

Using composer
```bash
composer require agussuroyo/eloquent-wpdb
```

# Usage

Boot it first
```php
$db = new \Agussuroyo\EloquentWpdb\DB();
$capsule = $db->capsule();
```

then do manual query builder
```php
// query builder
$con = $capsule->getConnection();
$con->table('tableName')->get();
```

or model based query
```php
// model based
class ModelName extends Illuminate\Database\Eloquent\Model
{
    
}

$all = ModelName::get();
$item = ModelName::find(123);
// do another Laravel query that we want...
```