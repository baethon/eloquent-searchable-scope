<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use Tests\Stubs\Post;

class SearchableTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }

    public function test_it_finds_models()
    {
        Post::insert([
            ['post' => 'Who does the gun belong to?'],
            ['post' => 'Look at that mountain.'],
            ['post' => 'I never for a moment imagined I\'d be able to afford to live in such a fancy house.'],
        ]);

        $results = Post::query()
            ->search('the gun')
            ->get();

        $this->assertEquals(1, $results->count());
    }
}
