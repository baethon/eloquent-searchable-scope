<?php

namespace Baethon\Laravel\Scopes;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    abstract public function getSearchableOptions(): SearchableOptions;

    public function scopeSearch(Builder $query, ?string $search, ?array $searchableFields = null)
    {
        $options = $this->getSearchableOptions();
        optional($searchableFields, $options->fields(...));

        if (! $options->hasSearchableFields()) {
            throw new \BadMethodCallException('You have to define at least one searchable field');
        }

        $searchWords = SearchableHelpers::getSearchTerms($search ?? '', $options);

        if ($searchWords->isEmpty()) {
            return;
        }

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        $operator = ($driver === 'pgsql')
            ? 'ILIKE'
            : 'LIKE';

        [$groupedRelations, $fields] = SearchableHelpers::splitSearchableFields($options);

        $applyWords = function ($field, $searchWords) use ($operator) {
            return fn ($innerQuery) => $searchWords->each(
                fn ($word) => $innerQuery->where($field, $operator, $word)
            );
        };

        $query->where(function (Builder $query) use ($searchWords, $fields, $groupedRelations, $applyWords) {
            $fields->each(fn ($field) => $query->orWhere($applyWords($field, $searchWords)));

            $groupedRelations->each(function ($fields, $relation) use ($searchWords, $query, $applyWords) {
                $query->orWhereHas($relation, function ($query) use ($fields, $searchWords, $applyWords) {
                    $query->where(function ($query) use ($fields, $searchWords, $applyWords) {
                        $fields->each(fn ($field) => $query->orWhere($applyWords($field, $searchWords)));
                    });
                });
            });
        });
    }
}
