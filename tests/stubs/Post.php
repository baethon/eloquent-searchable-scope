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

    public static function overloadSearchable(array $searchableFields): Post
    {
        return new class ($searchableFields) extends Post {
            protected $table = 'posts';

            public function __construct($searchableFields)
            {
                $this->searchable = $searchableFields;
            }
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
