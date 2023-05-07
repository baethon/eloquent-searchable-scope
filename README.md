# baethon/eloquent-searchable-scope

An Eloquent scope for building a search query using LIKE statements, which also supports searching using relations.

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
namespace App\Models;

use Baethon\Laravel\Scopes\Searchable;

class Post extends Model
{
    use Searchable;
}
```

Trait requires defining the `getSearchableOptions()` method:

```php
namespace App\Models;

use Baethon\Laravel\Scopes\Searchable;
use Baethon\Laravel\Scopes\SearchableOptions;

class Post extends Model
{
    use Searchable;
    
    public function getSearchableOptions(): SearchableOptions
    {
        return SearchableOptions::defaults()
            ->fields(['topic', 'text', 'user.email'];
    }
}
```

Note: `user.email` refers to `user` relation. It has to be defined in the model.

## Available Options

The `SearchableOptions` provides the ability to customize the search functionality in a few ways:

- `breakToWords()` - splits the search term into words and searches against each of them.
- `minTermLength(int $minLength)` - rejects any string/word that is shorter than the specified number of characters.
- `fields(array $fields)` - specifies the fields to be used in the search.

The `SearchableOptions::defaults()` is equivalent of:

```php
(new SearchableOptions)->minTermLength(3);
```

## Overloading search options

When using the `search()` scope, it is possible to define the searchable fields.

```php
$foundPosts = Post::query()
    ->search($search, [
        'title',
    ])
    ->get();
```

or, pass custom options object:

```php
$foundPosts = Post::query()
    ->search($search, SearchableOptions::defaults()->fields(['title'])
    ->get();
```

If passing a custom options object, ensure that the searchable fields are defined.

# Nothing new here!

The idea for this scope has been previously discussed in various places, such as [🔗 here](https://freek.dev/1182-searching-models-using-a-where-like-query-in-laravel) and [🔗 here](https://laravel-tricks.com/tricks/eloquents-dynamic-scope-search-trait). However, since it can be difficult to locate these resources every time one needs them, I have created a package that simplifies the installation process. It is important to note that this package does not introduce any novel concepts.

# Testing

```
composer test
```
