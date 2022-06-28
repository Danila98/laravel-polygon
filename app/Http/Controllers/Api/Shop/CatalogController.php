<?php

namespace App\Http\Controllers\Api\Shop;

use App\DataAdapter\ProductAdapter;
use App\Filter\ProductFilter;
use App\Http\Controllers\Controller;
use App\Models\Shop\Product;
use Illuminate\Http\Request;


class CatalogController extends Controller
{
    public function list(ProductFilter $filter, ProductAdapter $adapter)
    {
        sleep(3);
        $products = Product::filter($filter)->get();
        return $adapter->getArrayModelData($products);
    }
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
     */
    public function update(Request $request, Product $product)
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
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response('success', 201)->json([
            'success' => true,
        ]);
    }
}
