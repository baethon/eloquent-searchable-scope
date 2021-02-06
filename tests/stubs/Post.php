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

    public static function overloadSearchable(array $searchableFields, int $options = 0): Post
    {
        return new class ($searchableFields, $options) extends Post {
            protected $table = 'posts';

            protected int $searchOptions = 0;

            public function __construct($searchableFields, $options = 0)
            {
                $this->searchable = $searchableFields;
                $this->searchOptions = $options;
            }
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
