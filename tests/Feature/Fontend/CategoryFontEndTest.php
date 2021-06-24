<?php

namespace VCComponent\Laravel\Tag\Test\Feature\Option;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Category\Entities\Category;
use VCComponent\Laravel\Category\Test\TestCase;
use VCComponent\Laravel\Tag\Entities\Tag;


class CategoryFontEndTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */

    public function can_get_category_list_by_frontend_router()
    {
        // $category = factory(Category::class, 5)->make();
        // $response = $this->call('GET', 'api/post-management/admin/posts/');

        // $response->assertStatus(200);
    }

}
