# baethon/eloquent-searchable-scope

A dead-simple Eloquent scope that builds a search query using `LIKE` statements. Supports searching using relations.

```php
$foundPosts = Post::query()
    ->search($search)
    ->get();
```

# Installation

```
composer require baethon/eloquent-searchable-scope
```

# Model configuration

Import `Searchable` trait and use it in model:

```php
<?php

namespace App\Models;

use Baethon\Laravel\Scopes\Searchable;

class Post extends Model
{
    use Searchable;
}
```

Then, define the list of fields that should be used for searching.

```php
<?php

namespace App\Models;

use Baethon\Laravel\Scopes\Searchable;

class Post extends Model
{
    use Searchable;

    protected $searchable = ['topic', 'text', 'user.email'];
}
```

Note: `user.email` refers to `user` relation that has to be defined in the model.

## Breaking by words

By default, the scope will use the full search term in the query. To break the search term to words, you'll have to define `$searchOptions` property:

```php
<?php

namespace App\Models;

use Baethon\Laravel\Scopes\Searchable;
use Baethon\Laravel\Scopes\SearchableOptions;

class Post extends Model
{
    use Searchable;

    protected $searchable = ['topic', 'text', 'user.email'];

    protected $searchOptions = SearchableOptions::BREAK_WORDS;
}
```

Note: the scope will use only words with length >= 3.

# Nothing new here!

This scope has been discussed in [🔗 other](https://freek.dev/1182-searching-models-using-a-where-like-query-in-laravel) [🔗 places](https://laravel-tricks.com/tricks/eloquents-dynamic-scope-search-trait).

Every time I had to find them, so I decided to make a package that will be easy to install.

I didn't discover anything new here!

# Testing

```
composer test
```
