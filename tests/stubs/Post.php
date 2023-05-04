<?php

namespace Tests\Stubs;

use Baethon\Laravel\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Searchable;

    public $timestamps = false;

    protected $fillable = ['post'];

    protected $searchable = [
        'post',
    ];

    protected $searchOptions = 0;

    protected static ?array $searchableOverrides = null;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        optional(static::$searchableOverrides, function ($config) {
            $this->searchable = $config['searchableFields'];
            $this->searchOptions = $config['options'];
        });
    }

    public static function overloadSearchable(array $searchableFields, int $options = 0): Post
    {
        static::$searchableOverrides = compact('searchableFields', 'options');
        return new static();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
