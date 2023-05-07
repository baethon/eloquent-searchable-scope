<?php

namespace Tests;

use Baethon\Laravel\Scopes\SearchableOptions;
use Orchestra\Testbench\TestCase;

class SearchableOptionsTest extends TestCase
{
    public function test_it_creates_defaults_object()
    {
        $this->assertEquals(3, SearchableOptions::defaults()->getMinTermLength());
    }

    public function test_it_returns_break_to_words()
    {
        $this->assertFalse(SearchableOptions::defaults()->shouldBreakToWords());
        $this->assertTrue(SearchableOptions::defaults()->breakToWords()->shouldBreakToWords());
    }

    public function test_it_allows_setting_min_length()
    {
        $this->assertEquals(5, SearchableOptions::defaults()->minTermLength(5)->getMinTermLength());
    }

    public function test_it_allows_setting_fields()
    {
        $expected = ['name'];
        $this->assertEquals($expected, SearchableOptions::defaults()->fields($expected)->getFields());
    }
}
