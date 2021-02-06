<?php

namespace Tests;

use Orchestra\Testbench\TestCase;
use Tests\Stubs\Post;
use Tests\Stubs\Role;
use Tests\Stubs\User;

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

    public function test_it_finds_using_relation()
    {
        $jon = User::create(['email' => 'jon@stark.com']);
        $sansa = User::create(['email' => 'sansa@stark.com']);

        $jon->posts()->saveMany([
            new Post(['post' => 'Who does the gun belong to?']),
            new Post(['post' => 'Look at that mountain.']),
        ]);

        $sansa->posts()->saveMany([
            new Post(['post' => 'I never for a moment imagined I\'d be able to afford to live in such a fancy house.']),
        ]);

        $model = Post::overloadSearchable([
            'user.email',
        ]);

        $this->assertEquals(2, $model->newQuery()
            ->search('jon')
            ->count()
        );
        $this->assertEquals(0, $model->newQuery()
            ->search('the gun')
            ->count()
        );
    }

    public function test_it_combines_many_fields()
    {
        $jon = User::create(['email' => 'jon@stark.com']);
        $sansa = User::create(['email' => 'sansa@stark.com']);

        $jon->posts()->saveMany([
            new Post(['post' => 'Who does the gun belong to?']),
            new Post(['post' => 'Look at that mountain.']),
        ]);

        $sansa->posts()->saveMany([
            new Post(['post' => 'I never for a moment imagined I\'d be able to afford to live in such a fancy house.']),
            new Post(['post' => 'Who does the gun belong to?']),
        ]);

        $model = Post::overloadSearchable([
            'user.email',
            'post',
        ]);

        $this->assertEquals(2, $model->newQuery()
            ->search('jon')
            ->count()
        );
        $this->assertEquals(2, $model->newQuery()
            ->search('the gun')
            ->count()
        );
    }

    public function test_it_finds_using_nested_relations()
    {
        $admin = Role::create(['name' => 'admin']);
        $jon = $admin->users()->save(new User(['email' => 'jon@stark.com']));
        $sansa = User::create(['email' => 'sansa@stark.com']);

        $jon->posts()->saveMany([
            new Post(['post' => 'Who does the gun belong to?']),
        ]);

        $sansa->posts()->saveMany([
            new Post(['post' => 'I never for a moment imagined I\'d be able to afford to live in such a fancy house.']),
        ]);

        $model = Post::overloadSearchable([
            'user.role.name',
        ]);

        $this->assertEquals(1, $model->newQuery()
            ->search('admi')
            ->count()
        );
    }
}
