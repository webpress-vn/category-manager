# Category Manager Package for Laravel

- [Category Manager Package for Laravel](#category-manager-package-for-laravel)
  - [Installation](#installation)
    - [Composer](#composer)
    - [Service Provider](#service-provider)
    - [Config and Migration](#config-and-migration)
    - [Environment](#environment)
  - [Configuration](#configuration)
    - [URL namespace](#url-namespace)
    - [Model and Transformer](#model-and-transformer)
    - [Auth middleware](#auth-middleware)
  - [Query functions provide](#query-functions-provide)
    - [List of query functions](#list-of-query-functions)
    - [Use](#use)
    - [For example](#for-example)
  - [View](#view)
  - [Routes](#routes)


Category management package for managing category in laravel framework

## Installation

### Composer

To include the package in your project, Please run following command.

```
composer require vicoders/categorymanager
```

### Service Provider

In your  `config/app.php`  add the following Service Providers to the end of the  `providers`  array:

```php
'providers' => [
        ...
    VCComponent\Laravel\Category\Providers\CategoryServiceProvider::class,
    VCComponent\Laravel\Category\Providers\CategoryRouteProvider::class,
],
```

### Config and Migration

Run the following commands to publish configuration and migration files.

```
php artisan vendor:publish --provider="VCComponent\Laravel\Category\Providers\CategoryServiceProvider"
php artisan vendor:publish --provider="Dingo\Api\Provider\LaravelServiceProvider"
php artisan vendor:publish --provider "Prettus\Repository\Providers\RepositoryServiceProvider"
```
Create tables:

```
php artisan migrate
```

### Environment

In `.env` file, we need some configuration.

```
API_PREFIX=api
API_VERSION=v1
API_NAME="Your API Name"
API_DEBUG=false
```

## Configuration

### URL namespace

To avoid duplication with your application's api endpoints, the package has a default namespace for its routes which is  `category-management`. For example:

    {{url}}/api/category-management/admin/categories
You can modify the package url namespace to whatever you want by modifying the `CATEGORY_COMPONENT_NAMESPACE` variable in `.env` file.

    CATEGORY_COMPONENT_NAMESPACE="your-namespace"

### Model and Transformer

You can use your own model and transformer class by modifying the configuration file `config\category.php`

```php
'models'          => [
    'category' => App\Entities\Category::class,
],

'transformers'    => [
    'category' => App\Transformers\CategoryTransformer::class,
],
```
Your `Category` model class must implements `VCComponent\Laravel\Category\Contracts\CategorySchema` and `VCComponent\Laravel\Category\Contracts\CategoryManagement`

```php
<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use VCComponent\Laravel\Category\Contracts\CategoryManagement;
use VCComponent\Laravel\Category\Contracts\CategorySchema;
use VCComponent\Laravel\Category\Traits\CategoryManagementTrait;
use VCComponent\Laravel\Category\Traits\CategorySchemaTrait;

class Category extends Model implements Transformable, CategorySchema, CategoryManagement
{
    use TransformableTrait, CategorySchemaTrait, CategoryManagementTrait;

    const STATUS_PENDING = 1;
    const STATUS_ACTIVE  = 2;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'type',
        'type_post'
    ];
}
```

### Auth middleware

Configure auth middleware in configuration file `config\category.php`

```php
'auth_middleware' => [
        'admin'    => [
            'middleware' => 'jwt.auth',
            'except'     => ['index'],
        ],
        'frontend' => [
            'middleware' => 'jwt.auth',
            'except'     => ['index'],
        ],
],
```


## Query functions provide

### List of query functions
Get the list of categories of post type
```php
public function getCategoriesQuery(array $where, $number = 10, $order_by ='order', $order = 'asc', $columns = ['*']);

public function getCategoriesQueryPaginate(array $where, $number = 10, $order_by ='order', $order = 'asc', $columns = ['*']);
// Get a list of categories of a paginated post type
```
Get the category list of an article
```php
public function getPostCategoriesQuery($post_id, array $where, $post_type = 'posts', $number = 10, $order_by = 'order', $order = 'asc');

public function getPostCategoriesQueryPaginate($post_id, array $where, $post_type = 'posts', $number = 10, $order_by = 'order', $order = 'asc');
// get the category list of a paginated article
```
### Use

At controller use `CategoryRepository` and add function `__construct`
```php
use VCComponent\Laravel\Category\Repositories\CategoryRepository;
```
```php
public function __construct(CategoryRepository $categoryRepo)
{
    $this->categoryRepo = $categoryRepo;
}
```
### For example
```php
public function index() {
    $categories = $this->categoryRepo->getCategoriesQuery(['type'=>'knowledge'],0);
    // get all categories of post type knowledge (with $number = 0 get all records)
    $categoriesPaginate = $this->categoryRepo
    ->getCategoriesQueryPaginate(['type'=>'knowledge']);
    // get categories of paginated  knowledge post type
    $postCategories = $this->categoryRepo->getPostCategoriesQuery(45,['status'=>1]);
    // retrieve the categories of posts id = 45
    $postCategoriesPaginate = $this->categoryRepo
    ->getPostCategoriesQueryPaginate(45,['status'=>1]);
    // get the categories of posts id = 45 with pagination
}
```
## View

Your `CategoryListController` controller class must extends `VCComponent\Laravel\Category\Http\Controllers\Web\CategoryListController as BaseCategoryListController` implements `VCComponent\Laravel\Category\Contracts\ViewCategoryListControllerInterface;`

```php
class CategoryListController extends BaseCategoryListController implements ViewCategoryListControllerInterface
{
}
```

Your `CategoryDetailController` controller class must extends `VCComponent\Laravel\Category\Http\Controllers\Web\CategoryDetailController as BaseCategoryDetailController` implements `VCComponent\Laravel\Category\Contracts\ViewCategoryDetailControllerInterface;`

```php
class CategoryDetailController extends BaseCategoryDetailController implements ViewCategoryDetailControllerInterface
{
}
```

If you want change view default `CategoryList`, `CategoryDetail`, you must add the view your to the `Category` controller class.

```php
protected function view()
{
    return 'view-custom';
}
```
## Routes

The api endpoint should have these format:
| Verb   | URI                                            |
| ------ | ---------------------------------------------- |
| GET    | /api/{namespace}/admin/categories             |
| GET    | /api/{namespace}/admin/categories/{id}        |
| POST   | /api/{namespace}/admin/categories             |
| PUT    | /api/{namespace}/admin/categories/{id}        |
| DELETE | /api/{namespace}/admin/categories/{id}        |
| PUT    | /api/{namespace}/admin/categories/status/bulk |
| PUT    | /api/{namespace}/admin/categories/status/{id} |
| ----   | ----                                           |
| GET    | /api/{namespace}/categories                   |
| GET    | /api/{namespace}/categories/{id}              |
| POST   | /api/{namespace}/categories                   |
| PUT    | /api/{namespace}/categories/{id}              |
| DELETE | /api/{namespace}/categories/{id}              |
| PUT    | /api/{namespace}/categories/status/bulk       |
| PUT    | /api/{namespace}/categories/status/{id}       |
