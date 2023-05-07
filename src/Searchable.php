<?php

namespace Baethon\Laravel\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait Searchable
{
    abstract public function getSearchableOptions(): SearchableOptions;

    public function scopeSearch(Builder $query, ?string $search, ?array $searchableFields = null)
    {
        $options = $this->getSearchableOptions();
        optional($searchableFields, $options->fields(...));

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        $operator = ($driver === 'pgsql')
            ? 'ILIKE'
            : 'LIKE';

        $searchTerm = trim($search ?? '');

        if (! $searchTerm || mb_strlen($searchTerm) < $options->getMinTermLength()) {
            return;
        }

        $searchWords = $this->breakToWords($searchTerm, $options);

        $searchable = $options->getFields();
        [$relations, $fields] = collect($searchable)
            ->partition(fn ($field) => Str::contains($field, '.'));

        $groupedRelations = $relations
            ->groupBy(
                fn ($relation) => preg_replace('/\.\w+?$/', '', $relation)
            )
            ->map(fn ($list) => $list->map(
                fn ($relation) => preg_replace('/^.*\.(\w+)$/', '$1', $relation)
            ));

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

    private function breakToWords($searchTerm, SearchableOptions $options): Collection
    {
        if (! $options->shouldBreakToWords()) {
            return collect(["%{$searchTerm}%"]);
        }

        return collect(preg_split('/\s+/', $searchTerm))
            ->map(fn ($value) => trim($value))
            ->reject(fn ($value) => mb_strlen($value) < $options->getMinTermLength())
            ->map(fn ($value) => "%{$value}%");
    }
}
