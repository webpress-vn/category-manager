<?php

namespace VCComponent\Laravel\Category\Test\Feature\Fontend;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Category\Entities\Category;
use VCComponent\Laravel\Category\Test\TestCase;

class WebCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_get_list_categories_by_web_router()
    {
        $categories = factory(Category::class)->create()->toArray();
        unset($categories['updated_at']);
        unset($categories['created_at']);
        $response = $this->call('GET', 'category-management/categories');
        $response->assertStatus(200);
        $response->assertViewIs("category-manager::category-list");

    }

    /**
     * @test
     */
    public function can_get_a_category_by_web_router()
    {

        $category = factory(Category::class)->create()->toArray();
        unset($category['updated_at']);
        unset($category['created_at']);

        $response = $this->call('GET', 'category-management/categories/' . $category['slug']);

        $response->assertStatus(200);
        $response->assertViewIs("category-manager::category-detail");
        $response->assertViewHasAll([
            'category.name'     => $category['name'],
            'category.slug'     => $category['slug'],
        ]);
    }



}
