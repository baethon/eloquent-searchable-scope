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
}
