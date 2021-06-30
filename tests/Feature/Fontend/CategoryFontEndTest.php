<?php

namespace VCComponent\Laravel\Category\Test\Feature\Fontend;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Category\Entities\Category;
use VCComponent\Laravel\Category\Test\TestCase;



class CategoryFontEndTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_get_category_list_paginate_fontend()
    {
        $category = factory(Category::class, 5)->create();

        $category = $category->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($category, 'id');
        array_multisort($listIds, SORT_DESC, $category);

        $response = $this->call('GET', 'api/category-management/categories');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
            'meta' => [
                'pagination' => [
                    'total', 'count', 'per_page', 'current_page', 'total_pages', 'links' => [],
                ],
            ],
        ]);
        foreach ($category as $item) {
            $this->assertDatabaseHas('categories', $item);
        }
    }
    /**
     * @test
     */
    public function should_get_category_list_fontend()
    {
        $category = factory(Category::class, 5)->create();

        $category = $category->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($category, 'id');
        array_multisort($listIds, SORT_DESC, $category);

        $response = $this->call('GET', 'api/category-management/categories/all');

        $response->assertStatus(200);

        foreach ($category as $item) {
            $this->assertDatabaseHas('categories', $item);
        }
    }
    /**
     * @test
     */
    public function should_get_category_item_fontend()
    {
        $category = factory(Category::class)->create();

        unset($category['updated_at']);
        unset($category['created_at']);

        $response = $this->call('GET', 'api/category-management/categories/' . $category->id);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name'       => $category->name,
                'description' => $category->description,
            ],
        ]);
    }

    /**
     * @test
     */
    public function should_soft_delete_category_fontend()
    {
        $category = factory(Category::class)->create()->toArray();
        unset($category['updated_at']);
        unset($category['created_at']);

        $this->assertDatabaseHas('categories', $category);

        $response = $this->call('DELETE', 'api/category-management/categories/' . $category['id']);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDeleted('categories', $category);

    }
    /**
     * @test
     */
    public function should_create_category_fontend()
    {
        factory(Category::class)->create(['name' => 'category test']);

        $data = factory(Category::class)->make(['name' => 'category test'])->toArray();
        $response = $this->json('POST', 'api/category-management/categories', $data);
        $response->assertStatus(200);
        $response->assertJsonMissing(['slug' => 'category-test']);

        $data = factory(Category::class)->make(['name' => ''])->toArray();
        $response = $this->json('POST', 'api/category-management/categories', $data);
        $this->assertValidation($response, 'name', "The name field is required.");

        $data = factory(Category::class)->make()->toArray();
        $response = $this->json('POST', 'api/category-management/categories', $data);
        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('categories', $data);
    }
    /**
     * @test
     */
    public function should_update_status_category_fontend()
    {
        $category = factory(Category::class)->create()->toArray();
        unset($category['updated_at']);
        unset($category['created_at']);

        $this->assertDatabaseHas('categories', $category);

        $data     = ['status' => 2];
        $response = $this->json('PUT', 'api/category-management/categories/status/' . $category['id'], $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/category-management/categories/' . $category['id']);

        $response->assertJson(['data' => $data]);

    }
    /**
     * @test
     */

    public function should_bulk_update_status_category_by_fontend()
    {
        $categories = factory(Category::class, 5)->create();

        $categories = $categories->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($categories, 'id');

        $data    = ['item_ids' => $listIds, 'status' => 2];

        $response = $this->json('GET', 'api/category-management/categories/all');
        $response->assertJsonFragment(['status' => '1']);

        $response = $this->json('PUT', 'api/category-management/categories/status/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/category-management/categories');
        $response->assertJsonFragment(['status' => '2']);
    }
    /**
     * @test
     */
    public function should_update_category_fontend()
    {
        $category = factory(Category::class)->make();
        $category->save();
        unset($category['updated_at']);
        unset($category['created_at']);

        $id          = $category->id;
        $category->name = '';
        $data        = $category->toArray();
        $response = $this->json('PUT', 'api/category-management/categories/' . $id, $data);
        $this->assertValidation($response, 'name', "The name field is required.");

        $category->name = "update name";
        $data        = $category->toArray();

        $response = $this->json('PUT', 'api/category-management/categories/' . $id, $data);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name' => $data['name'],
            ],
        ]);

        $this->assertDatabaseHas('categories', $data);
    }
}
