<?php

namespace App\Http\Controllers\Api\Master;

use App\ProductPromos;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Filters\ProductFilters;
use App\Traits\StringTrait;
use DB;
use App\Product;

class ProductController extends Controller
{
    public function all($param)
    {
    	$data = Product::join('categories', 'products.category_id', '=', 'categories.id')
    				   ->join('groups', 'categories.group_id', '=', 'groups.id')
    				   ->join('group_products', 'groups.groupproduct_id', '=', 'group_products.id')
    				   ->where('groups.id', $param)
    				   ->select('products.id', 'products.model' , 'products.name', 'categories.name as category', 'groups.name as group', 'group_products.name as group_product')
    				   ->get();

    	foreach ($data as $item) {
            $item['promo'] = 0;
            if(ProductPromos::where('product_id', $item['id'])->count() > 0){
                $item['promo'] = 1;
            }
    	}

    	return response()->json($data);
    }

}
