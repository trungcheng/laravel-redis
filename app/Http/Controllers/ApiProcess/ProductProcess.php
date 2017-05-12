<?php

namespace App\Http\Controllers\ApiProcess;

use App\Http\Controllers\Controller;
use App\Models\Product;

use App\Http\Requests;
use App\Http\Requests\CreateEditProductRequest;
use Illuminate\Http\Request;

class ProductProcess extends Controller {

    function getList() {
        $products = Product::all();
        if (!empty($products) && !is_null($products)) {
            return response()->json(['status' => 200, 'data' => $products]);
        } else {
            return response()->json(['status' => 404, 'msg' => 'Data not found!']);
        }
    }

    function postCreate(CreateEditProductRequest $request) {
        try {
            $product = new Product();
            $product->name = $request->proName;
            $product->description = $request->proDesc;
            $product->price = $request->proPrice;

            if ($request->hasFile('proImage')) {
                $file = $request->file('proImage');
                $folder = public_path('uploads');
                if (!file_exists($folder)) { 
                    mkdir($folder, 0777, true); 
                }
                $filename = $file->getClientOriginalName();
                $pathFile = rand(100,10000).'-'.$filename;
                $product->image = '/uploads/'.$pathFile;
                $file->move($folder, $pathFile);
            }
            
            $product->save();

            return response()->json(['status' => 200, 'msg' => 'Post success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'msg' => $e->getMessage()]);
        }
    }

    function postEdit(CreateEditProductRequest $request, $id) {
        try {
            $product = Product::findOrFail($id);
            $product->name = $request->proName;
            $product->description = $request->proDesc;
            $product->price = $request->proPrice;

            if ($request->hasFile('proImage')) {    
                $file = $request->file('proImage');
                $folder = public_path('uploads');
                if (!file_exists($folder)) { 
                    mkdir($folder, 0777, true); 
                }
                $filename = $file->getClientOriginalName();
                $pathFile = rand(100,10000).'-'.$filename;
                $product->image = '/uploads/'.$pathFile;
                $file->move($folder, $pathFile);
            }
            
            $product->save();

            return response()->json(['status' => 200, 'msg' => 'Post success']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'msg' => $e->getMessage()]);
        }
    }

    function postDelete() {
        $data = json_decode(\Request::get('id'));
        $product = Product::find($data);
        if (!empty($product)) {
            if ($product->delete()) {
                return response()->json(['status' => 200, 'msg' => 'Delete product success!']);
            } else {
                return response()->json(['status' => 400, 'msg' => 'Data not found!']);
            }
        } else {
            return response()->json(['status' => 400, 'msg' => 'Data not found']);
        }
    }

}
