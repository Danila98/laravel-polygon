<?php

namespace App\Http\Controllers\Api\Shop;

use App\DataAdapter\ProductAdapter;
use App\Filter\ProductFilter;
use App\Http\Controllers\Controller;
use App\Models\Shop\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/catalog",
     *      operationId="getProductsList",
     *      tags={"Каталог"},
     *      security={ {"bearer": {} }},
     *      summary="Получить список продуктов",
     *      description="Вернет список продуктов",
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                property="products",
     *                type="array",
     *                example={{
     *                  "id": 1,
     *                  "name": "Fanger",
     *                  "image": "storage/images/test.png",
     *                  "price": 808.12,
     *                }, {
     *                  "id": "",
     *                  "name": "",
     *                  "image": "",
     *                  "price": ""
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
     *                         property="image",
     *                         type="string",
     *                         example=""
     *                      ),
     *                      @OA\Property(
     *                         property="price",
     *                         type="float",
     *                         example=""
     *                      ),
     *                      @OA\Property(
     *                         property="netPay",
     *                         type="money",
     *                         example=""
     *                      ),
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
    public function list(ProductFilter $filter, ProductAdapter $adapter)
    {
        sleep(3);
        $products = Product::filter($filter)->get();
        return ['products' => $adapter->getArrayModelData($products)];
    }
    /**
     * @OA\Get(
     *      path="/api/catalog/product/{product_id}",
     *      tags={"Каталог"},
     *      security={ {"bearer": {} }},
     *      summary="Получить товар по id",
     *      description="Вернет товар по id",
     *      @OA\Parameter(
     *      description="ID товарра",
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
     *              @OA\Property(property="image", type="string", example="storage/images/test.png"),
     *              @OA\Property(property="price", type="float", example=12.21),
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
    public function show(int $id, ProductAdapter $adapter)
    {
        if (!$product = Product::find($id))
        {
            return response('Not found', 404);
        }

        return $adapter->getModelData($product);
    }
    /**
     * Создает товар
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     * @OA\Post(
     * path="/api/catalog/add",
     * summary="Добавить продукт",
     * description="Нужно передать все поля",
     * tags={"Каталог"},
     * security={ {"bearer": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"name","price", "image"},
     *       @OA\Property(property="name", type="string",  example="test"),
     *       @OA\Property(property="price", type="float", example=65.21),
     *       @OA\Property(property="image", type="string", format="binary"),
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
            'price' => 'required|between:0,999999999.99',
            'image' => 'required|image',
        ]);
        $filenameWithExt = $request->file('image')->getClientOriginalName();
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $extention = $request->file('image')->getClientOriginalExtension();
        $fileNameToStore = "image/".$filename."_".time().".".$extention;
        $path = $request->file('image')->storeAs('public/', $fileNameToStore);
        $request =  $request->all();
        $product = Product::create([
            'name' => $request['name'],
            'price' => $request['price'],
            'img' => $path
        ]);
        return response('success', 201)->json([
            'success' => true,
            'product' => $product
        ]);

    }
    /**
     * Обновляет товар
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Shop\Product  $product
     * @return \Illuminate\Http\JsonResponse
     * @OA\Put(
     * path="/api/catalog/update/{product_id}",
     * summary="Обновить продукт",
     * description="Нужно передать все поля",
     * tags={"Каталог"},
     * security={ {"bearer": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"name","price"},
     *       @OA\Property(property="name", type="string",  example="test"),
     *       @OA\Property(property="price", type="float", example=65.21),
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
    public function update(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|between:0,999999999.99',
        ]);
        if( $request->hasFile('image')){
            $filenameWithExt = $request->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extention = $request->file('image')->getClientOriginalExtension();
            $fileNameToStore = "image/".$filename."_".time().".".$extention;
            $path = $request->file('image')->storeAs('public/', $fileNameToStore);
            $request =  $request->all();
            $product->update([
                'name' => $request['name'],
                'price' => $request['price'],
                'img' => $path
            ]);
        }else{
            $product->update($request->all());
        }
        return response('success', 201)->json([
            'success' => true,
            'product' => $product
        ]);
    }
    /**
     * Удаляет товар
     *
     * @param  \App\Models\Shop\Product  $product
     * @return \Illuminate\Http\JsonResponse
     * @OA\Delete(
     *      path="/api/catalog/delete/{product_id}",
     *      tags={"Каталог"},
     *      security={ {"bearer": {} }},
     *      summary="Удалить товар по id",
     *      description="Вернет булевое значение",
     *      @OA\Parameter(
     *      description="ID товарра",
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
    public function destroy(Product $product)
    {
        $product->delete();
        return response('success', 201)->json([
            'success' => true,
        ]);
    }
}
