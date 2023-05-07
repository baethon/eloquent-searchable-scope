<?php

namespace Baethon\Laravel\Scopes;

final class SearchableOptions
{
    private const BREAK_WORDS = 2 << 0;

    public function __construct(
        private int $options = 0,
        private int $minLength = 3,
        private array $fields = [],
    ) {
    }

    public static function defaults(): SearchableOptions
    {
        return new static(minLength: 3);
    }

    public function breakToWords(): SearchableOptions
    {
        $this->options |= static::BREAK_WORDS;

        return $this;
    }

    /**
     * Exclude strings shorter than the minimum length
     *
     * When using `breakToWords`, words shorter than the minimum length will be removed.
     */
    public function minTermLength(int $minLength): SearchableOptions
    {
        $this->minLength = $minLength;

        return $this;
    }

    public function fields(array $fields): SearchableOptions
    {
        $this->fields = $fields;

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getMinTermLength(): int
    {
        return $this->minLength;
    }

    public function shouldBreakToWords(): bool
    {
        return $this->options & static::BREAK_WORDS;
    }
}
