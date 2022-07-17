<?php

namespace App\Http\Controllers\Api\Accounting;

use App\DataAdapter\Accounting\CategoryAdapter;
use App\Http\Controllers\Api\ApiController;
use App\Models\Accounting\Category;
use App\Repository\Accounting\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    protected $categoryRepository;
    protected $categoryAdapter;
    public function __construct(CategoryRepository $categoryRepository, CategoryAdapter $adapter)
    {
        $this->categoryRepository = $categoryRepository;
        $this->categoryAdapter = $adapter;
    }
      /**
 * @OA\Get(
 *      path="/api/category",
 *      tags={"Категории"},
 *      security={ {"bearer": {} }},
 *      summary="Получить список Категорий",
 *      description="Вернет список Категорий",
 *      @OA\Response(
 *          response=200,
 *          description="Success",
 *          @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                property="categories",
 *                type="array",
 *                example={{
 *                  "id": 1,
 *                  "name": "Fanger",
 *                  "limit": 12312,
 *                }, {
 *                  "id": "",
 *                  "name": "",
 *                  "limit": "",
 *                }},
 *                @OA\Items(
 *                      @OA\Property(
 *                         property="id",
 *                         type="int",
 *                         example=""
 *                      ),
 *                      @OA\Property(
 *                         property="name",
 *                         type="string",
 *                         example=""
 *                      ),
 *                      @OA\Property(
 *                         property="limit",
 *                         type="int",
 *                         example=""
 *                      )
 *                ),
 *             ),
 *     ),
 *       ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthenticated",
 *      ),
 *      @OA\Response(
 *          response=403,
 *          description="Forbidden"
 *      )
 *     )
 */
    public function list()
    {
        $categories = $this->categoryRepository->getByUser(auth('api')->user());
        return $this->sendResponse(200, ['categories' => $this->categoryAdapter->getArrayModelData($categories)]);
    }
    /**
     * @OA\Get(
     *      path="/api/category/{category_id}",
     *      tags={"Категории"},
     *      security={ {"bearer": {} }},
     *      summary="Получить категорию по id",
     *      description="Вернет категорию по id",
     *      @OA\Parameter(
     *      description="ID категории",
     *      in="path",
     *      name="id",
     *      required=true,
     *      example="1",
     *      @OA\Schema(
     *          type="integer",
     *           format="int64"
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="int", example=1),
     *              @OA\Property(property="name", type="string", example="test"),
     *              @OA\Property(property="limit", type="int", example=123),
     *          ),
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found"
     *      )
     *     )
     */
    public function show(int $id)
    {
        if (!$category = Category::find($id))
        {
            return $this->sendError( 404, 'Not found');
        }

        return $this->sendResponse(200, ['category' => $this->categoryAdapter->getModelData($category)]);
    }
    /**
     * Создает Категорию
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @OA\Post(
     * path="/api/category/add",
     * summary="Добавить категорию",
     * description="Нужно передать все поля",
     * tags={"Категории"},
     * security={ {"bearer": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"name","limit"},
     *       @OA\Property(property="name", type="string",  example="test"),
     *       @OA\Property(property="limit", type="int", example=6521),
     *    ),
     * ),
     * @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Вернет ошибку валидации, если поля не валидны или какие-то не отправлены",
     * )
     * )
     */

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'limit' => 'required|numeric',
        ]);
        $request =  $request->all();
        $category = Category::create([
            'name' => $request['name'],
            'limit' => $request['limit'],
            'user_id' => auth('api')->user()->id
        ]);
        return $this->sendResponse(201, ['category' => $category]);
    }
    /**
     * Обновляет Категрию
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Accounting\Category  $Category
     * @return \Illuminate\Http\JsonResponse
     * @OA\Put(
     * path="/api/category/update/{category_id}",
     * summary="Обновить Категрию",
     * description="Нужно передать все поля",
     * tags={"Категории"},
     * security={ {"bearer": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"name","limit"},
     *       @OA\Property(property="name", type="string",  example="test"),
     *       @OA\Property(property="limit", type="int", example=6521),
     *    ),
     * ),
     * @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Вернет ошибку валидации, если поля не валидны или какие-то не отправлены",
     * )
     * )
     */
    public function update(Request $request, int $category_id): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'limit' => 'required|numeric',
        ]);
        $category = Category::find($category_id);
        $category->update($request->all());
        return $this->sendResponse(201, ['category' => $category]);
    }
    /**
     * Удаляет Категорию
     *
     * @param  \App\Models\Shop\Product  $product
     * @return \Illuminate\Http\JsonResponse
     * @OA\Delete(
     *      path="/api/category/delete/{category_id}",
     *      tags={"Категории"},
     *      security={ {"bearer": {} }},
     *      summary="Удалить категорию по id",
     *      description="Вернет булевое значение",
     *      @OA\Parameter(
     *      description="ID категории",
     *      in="path",
     *      name="id",
     *      required=true,
     *      example="1",
     *      @OA\Schema(
     *          type="integer",
     *           format="int64"
     *        )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Success",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="bool", example=true),
     *          ),
     *       ),
     * @OA\Response(
     *     response=401,
     *     description="Unauthenticated",
     * ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found"
     *      )
     *     )
     */
    public function destroy(int $category_id)
    {
        $category = Category::find($category_id);
        $category->delete();
        return $this->sendResponse(201);
    }
}
