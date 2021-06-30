<?php

namespace VCComponent\Laravel\Category\Test\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use VCComponent\Laravel\Category\Test\TestCase;
use VCComponent\Laravel\Category\Entities\Category;


class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_create_category_admin()
    {
        factory(Category::class)->create(['name' => 'category test']);
        $data = factory(Category::class)->make(['name' => 'category test'])->toArray();
        $response = $this->json('POST', 'api/category-management/admin/categories', $data);
        $response->assertStatus(500);
        $response->assertJson([
            'message' => 'Tên danh mục không được để trùng nhau'
        ]);

        $data = factory(Category::class)->make(['name' => 'category test', 'type' => 'posts'])->toArray();
        $response = $this->json('POST', 'api/category-management/admin/categories', $data);
        $response->assertStatus(200);
        $response->assertJsonMissing(['slug' => 'category-test']);

        $data = factory(Category::class)->make(['name' => ''])->toArray();
        $response = $this->json('POST', 'api/category-management/admin/categories', $data);
        $this->assertValidation($response, 'name', "The name field is required.");

        $data = factory(Category::class)->make()->toArray();
        $response = $this->json('POST', 'api/category-management/admin/categories', $data);
        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('categories', $data);
    }
    /**
     * @test
     */
    public function should_update_category_admin()
    {
        factory(Category::class)->create(['name' => 'category test']);

        $category = factory(Category::class)->make();
        $category->save();
        unset($category['updated_at']);
        unset($category['created_at']);

        $id          = $category->id;
        $category->name = 'category test';
        $data        = $category->toArray();
        $response = $this->json('PUT', 'api/category-management/admin/categories/' . $id, $data);

        $response->assertStatus(500);
        $response->assertJson([
            'message' => 'Tên danh mục không được để trùng nhau'
        ]);

        $category->name = "update name";
        $data        = $category->toArray();

        $response = $this->json('PUT', 'api/category-management/admin/categories/' . $id, $data);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'name' => $data['name'],
            ],
        ]);

        $this->assertDatabaseHas('categories', $data);
    }
      /**
     * @test
     */
    public function should_soft_delete_category_admin()
    {
        $category = factory(Category::class)->create()->toArray();
        unset($category['updated_at']);
        unset($category['created_at']);

        $this->assertDatabaseHas('categories', $category);

        $response = $this->call('DELETE', 'api/category-management/admin/categories/' . $category['id']);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDeleted('categories', $category);

    }

      /**
     * @test
     */
    public function should_get_category_list_paginate_admin()
    {
        $category = factory(Category::class, 5)->create();

        $category = $category->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($category, 'id');
        array_multisort($listIds, SORT_DESC, $category);

        $response = $this->call('GET', 'api/category-management/admin/categories');


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
    public function should_get_category_item_admin()
    {
        $category = factory(Category::class)->create();

        unset($category['updated_at']);
        unset($category['created_at']);

        $response = $this->call('GET', 'api/category-management/admin/categories/' . $category->id);

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
    public function should_get_category_list_admin()
    {
        $category = factory(Category::class, 5)->create();

        $category = $category->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $response = $this->call('GET', 'api/category-management/admin/categories/all');
        $response->assertStatus(200);

        foreach ($category as $item) {
            $this->assertDatabaseHas('categories', $item);
        }
    }
    /**
     * @test
     */
    public function should_update_status_category_admin()
    {
        $category = factory(Category::class)->create()->toArray();
        unset($category['updated_at']);
        unset($category['created_at']);

        $this->assertDatabaseHas('categories', $category);

        $data     = ['status' => 2];
        $response = $this->json('PUT', 'api/category-management/admin/categories/status/' . $category['id'], $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/category-management/admin/categories/' . $category['id']);

        $response->assertJson(['data' => $data]);

    }
    /**
     * @test
     */

    public function should_bulk_update_status_category_by_admin()
    {
        $categories = factory(Category::class, 5)->create();

        $categories = $categories->map(function ($e) {
            unset($e['updated_at']);
            unset($e['created_at']);
            return $e;
        })->toArray();

        $listIds = array_column($categories, 'id');

        $data    = ['item_ids' => $listIds, 'status' => 2];

        $response = $this->json('GET', 'api/category-management/admin/categories/all');
        $response->assertJsonFragment(['status' => '1']);

        $response = $this->json('PUT', 'api/category-management/admin/categories/status/bulk', $data);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $response = $this->json('GET', 'api/category-management/admin/categories');
        $response->assertJsonFragment(['status' => '2']);
    }
    /**
     * @test
     */
    public function should_bulk_move_category_admin()
    {
        $listCategories = [];
        for ($i = 0; $i < 5; $i++) {
            $categories = factory(Category::class)->create()->toArray();
            unset($categories['updated_at']);
            unset($categories['created_at']);
            array_push($listCategories, $categories);
        }

        $listIds = array_column($listCategories, 'id');
        $data    = ["ids" => $listIds];

        $response = $this->call('DELETE', 'api/category-management/admin/categories/bulk-delete', $data);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        foreach ($listCategories as $item) {
            $this->assertDeleted('categories', $item);
        }
    }


}
