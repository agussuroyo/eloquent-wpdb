# $wpdb with Eloquent from Laravel

[![PHP Validator](https://github.com/jonyextenz/eloquent-wpdb/actions/workflows/php.yml/badge.svg)](https://github.com/jonyextenz/eloquent-wpdb/actions/workflows/php.yml)

- I made this just for personal use
- I do not guarantee anything for the use of this software
- Do It With Your Own Risk

# Usage

```php
$db = new \Agussuroyo\EloquentWpdb\DB();
$capsule = $db->capsule();

// 1. manual query builder
$con = $capsule->getConnection();
$con->from('tableName')->get();

// or 

// 2. model based

class ModelName extends Illuminate\Database\Eloquent\Model
{
    
}

$all = ModelName::get();
$item = ModelName::find(123);
// do another Laravel query that we want...
```