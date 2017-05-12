<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Cache\CacheRedis;

class ProductController extends Controller
{
    public function getIndex() {
        $this->authorize('ViewProduct');

        if (auth()->user()->user_type == 'Admin' || auth()->user()->user_type == 'Editor') {
            $products = Product::join('users', 'products.creator', '=', 'users.email')
                ->select('products.*')->get();
        } else {
            $products = Product::join('users', 'products.creator', '=', 'users.email')
                ->select('products.*')->where('creator', auth()->user()->email)->get();
        }

        if(!empty($products)) {
            return view('childs.product.index')->with('products', $products);
        } else {
            echo "No data";
        }
    }

    public function getCreate()
    {
        $this->authorize('CreateProduct');

        return view('childs.product.create');
    }

    public function postCreate(Request $request) {
        $this->authorize('SaveProduct');

        try {
        	$product = new Product();

        	$product->name = $request->proName;
            $product->description = $request->proDesc;
            $product->price = $request->proPrice;
            $product->point = $request->proPoint;
            $product->creator = auth()->user()->email;
            $product->thumbnail = !empty($request->thumbnail) ? $request->thumbnail : '';
            $product->save();

            $cache = new CacheRedis();
        	$cache->CacheProduct($product);

	        return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function getEdit($id) {
        $this->authorize('SaveProduct');

        $product = Product::findOrFail($id);
        return view('childs.product.edit')
            ->with('product', $product);
    }

    public function postEdit(Request $request, $id) {
        $this->authorize('SaveCategory');
        if (!isset($id)) {
            abort(404);
        }
        try {
        	$product = Product::findOrFail($id);

        	$product->name = $request->proName;
            $product->description = $request->proDesc;
            $product->price = $request->proPrice;
            $product->point = $request->proPoint;
            $product->creator = auth()->user()->email;
            $product->thumbnail = !empty($request->thumbnail) ? $request->thumbnail : '';
            $product->save();

            $cache = new CacheRedis();
        	$cache->CacheProduct($product);

	        return json_encode(['status' => 'success', 'msg' => 'Edit Successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postDelete(Request $request)
    {
        $this->authorize('SaveCategory');
        if (!$request->has('id')) {
            abort(404);
        }
        $id = $request->get('id');
        $product = Product::findOrFail($id);

        $result = array(
            'status' => 'error',
            'msg' => trans('product.del_pro_fail')
        );
        try {
            if ($product->delete()) {

            	$cache = new CacheRedis();
                $cache->RemoveKey(env('REDIS_PREFIX') . '_product_detail_' . $id);
        		$cache->RemoveKey(env('REDIS_PREFIX') . '_user_product_' . auth()->user()->id);

                $result = array(
                    'status' => 'success',
                    'msg' => trans('product.del_pro_success')
                );
            }

            return json_encode($result);
        } catch (\Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }
}
