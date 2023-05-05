<?php

namespace Tests\Stubs;

use Baethon\Laravel\Scopes\Searchable;
use Baethon\Laravel\Scopes\SearchableOptions;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Searchable;

    public $timestamps = false;

    protected $fillable = ['post'];

    public static ?SearchableOptions $searchableOverride = null;

    public function getSearchableOptions(): SearchableOptions
    {
        return static::$searchableOverride
            ?? SearchableOptions::defaults()
                ->fields(['post']);
    }

    public static function overloadSearchable(SearchableOptions $options): Post
    {
        static::$searchableOverride = $options;
        return new static();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
