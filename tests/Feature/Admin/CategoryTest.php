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
    public function can_create_category_admin()
    {
        $data = factory(Category::class)->make()->toArray();
        $response = $this->json('POST', 'api/category-management/admin/categories', $data);
        $response->assertStatus(200);
        $response->assertJson(['data' => $data]);

        $this->assertDatabaseHas('categories', $data);
    }
    /**
     * @test
     */
    public function can_update_category_admin()
    {
        $category = factory(Category::class)->make();
        $category->save();

        unset($category['updated_at']);
        unset($category['created_at']);

        $id          = $category->id;
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
    public function can_soft_delete_category_admin()
    {
        $category = factory(Category::class)->create()->toArray();
        unset($category['updated_at']);
        unset($category['created_at']);

        $this->assertDatabaseHas('categories', $category);

        $response = $this->call('DELETE', 'api/category-management/admin/categories/' . $category['id']);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        // $this->assertSoftDeleted('categories', $category);

    }

      /**
     * @test
     */
    public function can_get_category_list_admin()
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

        foreach ($category as $item) {
            $this->assertDatabaseHas('categories', $item);
        }
    }

     /**
     * @test
     */
    public function can_get_category_item_admin()
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
    public function can_get_category_analytics_admin()
    {
        $category = factory(Category::class, 5)->create();
        $response = $this->call('GET', 'api/category-management/admin/categories/analytics');
        dd($response);
        $response->assertStatus(200);


    }

}
