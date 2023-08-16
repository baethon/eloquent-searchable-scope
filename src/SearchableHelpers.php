<?php

namespace Baethon\Laravel\Scopes;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class SearchableHelpers
{
    public static function getSearchTerms(string $search, SearchableOptions $options): Collection
    {
        $terms = Str::of($search)
            ->trim()
            ->when($options->shouldBreakToWords(), fn (Stringable $str) => $str->split('/\s+/'));

        return Collection::wrap($terms)
            ->filter(fn ($term) => mb_strlen($term) >= $options->getMinTermLength())
            ->map(fn ($term) => "%{$term}%");
    }

    public static function splitSearchableFields(SearchableOptions $options): array
    {
        [$relations, $fields] = collect($options->getFields())
            ->partition(fn ($field) => Str::contains($field, '.'));

        $groupedRelations = $relations
            ->groupBy(
                fn ($relation) => Str::beforeLast($relation, '.'),
            )
            ->map(fn ($list) => $list->map(
                fn ($relation) => Str::afterLast($relation, '.'),
            ));

        return [$groupedRelations, $fields];
    }
}
