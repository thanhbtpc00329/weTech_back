<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Product;
use App\Category;
use App\Bill;
use App\Product_detail;
use App\Shop;
use App\Product_image;
use App\Banner;
use App\Order;
use DB;
use Cloudder;
use Response,File;


class ProductController extends Controller
{
    // Product
    public function showProduct()
    {
        
        $pro = DB::table('product_detail')->get();
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $now = time();
        
        for ($i=0; $i < count($pro); $i++) { 
            // To time
            $ttime = $pro[$i]->updated_at;
            $ttime = date_parse_from_format('Y-m-d H:i:s', $ttime);
            $ttime_stamp = mktime($ttime['hour'],$ttime['minute'],$ttime['second'],$ttime['month'],$ttime['day'],$ttime['year']);
            if($now >= $ttime_stamp){
                $prod = Product_detail::where('prodetail_id',$pro[$i]->prodetail_id)->update(['status_discount' => 0]);
            }
        }

        $product = DB::table('products')
            ->join('product_detail','product_detail.product_id','=','products.product_id')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->groupBy('product_detail.product_id')
            ->where('product_detail.status_confirm','=',1)
            ->paginate(20);
        
        return response()->json($product);
    }

    public function productType(Request $request){
        $cate_id = $request->cate_id;

        $product = DB::table('products')
            ->join('product_detail','product_detail.product_id','=','products.product_id')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->where('products.cate_id',$cate_id)
            ->where('product_detail.status_confirm','=',1)
            ->groupBy('product_detail.product_id')
            ->get();
        return response()->json($product);
    }

    public function showProductShop(Request $request){
        $shop_id = $request->shop_id;

        $product = DB::table('products')
            ->join('categories','products.cate_id','=','categories.cate_id')
            ->join('product_detail','product_detail.product_id','=','products.product_id')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->where('products.shop_id','=',$shop_id)
            ->where('product_detail.status_confirm','=',1)
            ->groupby('product_detail.product_id')
            ->paginate(5);
        return response()->json($product);
    }


    public function productCate(Request $request){
        $cate_name = $request->cate_name;

        $cate = Category::where('cate_name',$cate_name)->get();

        $product = DB::table('products')
            ->join('product_detail','product_detail.product_id','=','products.product_id')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->where('products.cate_id',$cate[0]->cate_id)
            ->where('product_detail.status_confirm','=',1)
            ->groupBy('product_detail.product_id')
            ->get();
        return response()->json($product);
    }

    public function searchCate(Request $request){
        $keywords = $request->keywords;

        $cate = Category::where('cate_name','like','%'.$keywords.'%')->get();
        $kq = array();
        for ($i=0; $i < count($cate); $i++) { 
            $product = DB::table('products')
            ->join('product_detail','product_detail.product_id','=','products.product_id')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->where('products.cate_id',$cate[$i]->cate_id)
            ->where('product_detail.status_confirm','=',1)
            ->groupBy('product_detail.product_id')
            ->get();
            if(count($product) > 0){
                array_push($kq,$product);
            }
        }
        $str1 = json_encode($kq);
        $str2 = str_replace(array('[[',']]','],['),array('[',']',','),$str1);
        $str3 = json_decode($str2);
        return response()->json($str3);
    }


    public function searchCategory(Request $request){
        $keywords = $request->keywords;

        $cate = Category::where('category','like','%'.$keywords.'%')->get();
        $kq = array();
        for ($i=0; $i < count($cate); $i++) { 
            $product = DB::table('products')
            ->join('product_detail','product_detail.product_id','=','products.product_id')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->where('products.cate_id',$cate[$i]->cate_id)
            ->where('product_detail.status_confirm','=',1)
            ->groupBy('product_detail.product_id')
            ->get();
            if(count($product) > 0){
                array_push($kq,$product);
            }
        }
        $str1 = json_encode($kq);
        $str2 = str_replace(array('[[',']]','],['),array('[',']',','),$str1);
        $str3 = json_decode($str2);
        return response()->json($str3);
    }


    public function searchProduct(Request $request){
        $keywords = $request->keywords;

        $product = DB::table('products')
            ->join('product_detail','product_detail.product_id','=','products.product_id')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->where('products.product_name','like','%'.$keywords.'%')
            ->where('product_detail.status_confirm','=',1)
            ->groupBy('product_detail.product_id')
            ->get();
        return response()->json($product); 
    }


    public function productCategory(Request $request){
        $category = $request->category;

        $cate = Category::where('category',$category)->get();
        $kq = array();
        for ($i=0; $i < count($cate); $i++) { 
            $product = DB::table('products')
            ->join('product_detail','product_detail.product_id','=','products.product_id')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->where('products.cate_id',$cate[$i]->cate_id)
            ->where('product_detail.status_confirm','=',1)
            ->groupBy('product_detail.product_id')
            ->get();
            if(count($product) > 0){
                array_push($kq,$product);
            }
        }
        $str1 = json_encode($kq);
        $str2 = str_replace(array('[[',']]','],['),array('[',']',','),$str1);
        $str3 = json_decode($str2);
        // $str4 = array();
        // for ($j=0; $j < 15; $j++) { 
        //     array_push($str4,$str3[$j]);
        // }
        return response()->json($str3);
    }


    public function productShop(Request $request){
        $shop_id = $request->shop_id;

        $product = DB::table('product_detail')
            ->join('products','product_detail.product_id','=','products.product_id')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->where('products.shop_id','=',$shop_id)
            ->where('product_detail.status_confirm','=',1)
            ->groupby('product_image.prodetail_id')
            ->get();
        return response()->json($product);
    }


    public function showDetail(Request $request){
        $id = $request->id;

        $detail = DB::table('products')
            ->join('categories','categories.cate_id','=','products.cate_id')
            ->join('product_detail','product_detail.product_id','=','products.product_id')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->join('users','users.user_id','=','shops.user_id')
            ->where('products.product_id','=',$id)
            ->where('product_detail.status_confirm','=',1)
            ->get();
            
            return response()->json($detail);
    }


    public function detailProductShop(Request $request){
        $product_id = $request->product_id;

        $detail = DB::table('products')
            ->join('categories','categories.cate_id','=','products.cate_id')
            ->join('product_detail','product_detail.product_id','=','products.product_id')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->join('users','users.user_id','=','shops.user_id')
            ->where('products.product_id','=',$product_id)
            ->where('product_detail.status_confirm','=',1)
            ->groupby('product_image.prodetail_id')
            ->get();
            
            return response()->json($detail);
    }


    public function detailInfo(Request $request){
        $id = $request->id;

        $detail = DB::table('product_detail')
            ->join('product_image','product_detail.prodetail_id','=','product_image.prodetail_id')
            ->groupby('product_image.prodetail_id')
            ->where('product_id',$id)
            ->where('product_detail.status_confirm','=',1)
            ->get();
            $da = json_decode($detail);
            
            $arr = Schema::getColumnListing('product_detail');
            $da4 = [];
            for ($j=0; $j < count($da) ; $j++) { 
                $da2 = $da[$j];
                for ($i= 2; $i <= 22 ; $i++) {
                    $x = $arr[$i];
                    unset($da2->product_id);
                    if ($arr[$i] != $arr[6]) { 
                        if($da2->$x == null){
                            unset($da2->$x);
                        }
                    }
                    else{unset($da2->status);}
                }
                array_push($da4,$da2);
            }
            $da3 = $da4;
            
            return response()->json($da3);
    }

    public function detailImage(Request $request){
        $prodetail_id = $request->id;

        $prod = DB::table('product_detail')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('products','products.product_id','=','product_detail.product_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->where('product_detail.prodetail_id','=',$prodetail_id)
            ->where('product_detail.status_confirm','=',1)
            ->first();
        return response()->json($prod);
    }

    public function uploadProductImage(Request $request){
        $image = $request->file('image');
        if ($image) {
            //get name image
            $filename =$request->file('image');
            $name = $filename->getClientOriginalName();
            $extension = $filename->getClientOriginalExtension();
            $cut = substr($name, 0,strlen($name)-(strlen($extension)+1));
            //upload image
            Cloudder::upload($filename, 'products/' . $cut); 
            list($width, $height) = getimagesize($filename);        
        }

        return Cloudder::show('products/'. $cut, ['width'=>$width,'height'=>$height]);
    }

    
    public function addProduct(Request $request){
        $ch1 = '01234567890123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $ch1len = strlen($ch1);
            $rd = '';
            for ($i = 0; $i < 4; $i++) {
                $rd .= $ch1[rand(0, $ch1len - 1)].rand(0,9).rand(0,9);
            }

        $id = 'SP_'.$rd;

        $timedt = now()->timezone('Asia/Ho_Chi_Minh');

        $product_name = $request->product_name;
        $brand = $request->brand;
        $cate_id = $request->cate_id;
        $introduction = $request->introduction;
        $description = $request->description;
        $tag = $request->tag;
        $shop_id = $request->shop_id;

        $prod = new Product;
        $prod->product_id = $id;
        $prod->product_name = $product_name;
        $prod->brand = $brand;
        $prod->cate_id = $cate_id;
        $prod->introduction = $introduction;
        $prod->description = $description;
        $prod->tag = $tag;
        $prod->shop_id = $shop_id;
        $prod->created_at = now()->timezone('Asia/Ho_Chi_Minh');

        $prod->save();
        
        
        $price = $request->price;
        $color = $request->color;
        $quantity = $request->quantity;
        $size = $request->size;
        $percent = $request->percent;
        $origin = $request->origin;
        $accessory = $request->accessory;
        $dimension = $request->dimension;
        $weight = $request->weight;
        $system = $request->system;
        $material = $request->material;
        $screen_size = $request->screen_size;
        $wattage = $request->wattage;
        $volume = $request->volume;
        $resolution = $request->resolution;
        $memory = $request->memory;
        $from_day = $request->from_day;
        $to_day = $request->to_day;

        $rd1 = '';
            for ($i = 0; $i < 4; $i++) {
                $rd1 .= $ch1[rand(0, $ch1len - 1)].rand(0,9).rand(0,9);
            }
            
        $id1 = 'CT_'.$rd1;  
       

        $pro = new Product_detail;
        $pro->prodetail_id = $id1;
        $pro->product_id = $id;
        $pro->price = $price;
        $pro->color = $color;
        $pro->quantity = $quantity;
        $pro->size = $size;
        $pro->status_discount = 0;
        $pro->status_confirm = 0;
        $pro->origin = $origin;
        $pro->accessory = $accessory;
        $pro->dimension = $dimension;
        $pro->weight = $weight;
        $pro->system = $system;
        $pro->material = $material;
        $pro->screen_size = $screen_size;
        $pro->wattage = $wattage;
        $pro->volume = $volume;
        $pro->resolution = $resolution;
        $pro->memory = $memory;
        if($from_day){
            $pro->percent = $percent;
            $pro->discount_price = $price - ($price * ($percent / 100));
            $pro->created_at = str_replace('T',' ',$from_day);
            $pro->updated_at = str_replace('T',' ',$to_day);
        }
        
        $pro->save();


        $image = $request->image;
        if ($image) {
            $tt = ltrim($image,'"[');
            $pp = rtrim($tt,'"]');
            $arr = explode('","', $pp);
            for ($i=0; $i < count($arr); $i++) { 
                $bt = ltrim($arr[$i],'"');
                $bp = rtrim($bt,'"');
                $pro_img = new Product_image;
                $pro_img->prodetail_id = $id1;
                $pro_img->image = $bp;
                $pro_img->created_at = $timedt;
                $pro_img->save();
            }
        }

            $sp = Product_detail::where('prodetail_id',$id1)->first();
                date_default_timezone_set('Asia/Ho_Chi_Minh');
            $now = time();
        if($sp->created_at){
            // From time
            $ftime = $sp->created_at;
            $ftime = date_parse_from_format('Y-m-d H:i:s', $ftime);
            $ftime_stamp = mktime($ftime['hour'],$ftime['minute'],$ftime['second'],$ftime['month'],$ftime['day'],$ftime['year']);
            // To time
            $ttime = $sp->updated_at;
            $ttime = date_parse_from_format('Y-m-d H:i:s', $ttime);
            $ttime_stamp = mktime($ttime['hour'],$ttime['minute'],$ttime['second'],$ttime['month'],$ttime['day'],$ttime['year']);
            if($now >= $ftime_stamp && $now <= $ttime_stamp){
                $sp->status_discount = 1;
                $sp->save();
            }else{
                $sp->status_discount = 0;
                $sp->save();
            }
        }
        
        if ($pro_img) {
            return response()->json(['success' => 'Thêm sản phẩm thành công!']);  
        }
        else{
            return response()->json(['error' => 'Thêm thất bại']);
        }
    }

    public function prodetailShop(Request $request)
    {
        $prodetail_id = $request->prodetail_id;

        $pro = DB::table('product_detail')
                ->join('products','products.product_id','=','product_detail.product_id')
                ->join('categories','categories.cate_id','=','products.cate_id')
                ->where('product_detail.prodetail_id','=',$prodetail_id)
                ->get();
        return response()->json($pro);
    }

    public function imageDetailShop(Request $request){
        $prodetail_id = $request->prodetail_id;

        $img = Product_image::where('prodetail_id',$prodetail_id)->get();
        return response()->json($img);
    }


    public function addDetail(Request $request){
        $ch1 = '01234567890123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $ch1len = strlen($ch1);

        $timedt = now()->timezone('Asia/Ho_Chi_Minh');
        
        $product_id = $request->product_id;
        $price = $request->price;
        $color = $request->color;
        $quantity = $request->quantity;
        $size = $request->size;
        $percent = $request->percent;
        $origin = $request->origin;
        $accessory = $request->accessory;
        $dimension = $request->dimension;
        $weight = $request->weight;
        $system = $request->system;
        $material = $request->material;
        $screen_size = $request->screen_size;
        $wattage = $request->wattage;
        $volume = $request->volume;
        $resolution = $request->resolution;
        $memory = $request->memory;
        $from_day = $request->from_day;
        $to_day = $request->to_day;

        $rd1 = '';
            for ($i = 0; $i < 4; $i++) {
                $rd1 .= $ch1[rand(0, $ch1len - 1)].rand(0,9).rand(0,9);
            }
            
        $id1 = 'CT_'.$rd1;  
     

        $pro = new Product_detail;
        $pro->prodetail_id = $id1;
        $pro->product_id = $product_id;
        $pro->price = $price;
        $pro->color = $color;
        $pro->quantity = $quantity;
        $pro->size = $size;
        $pro->status_discount = 0;
        $pro->status_confirm = 0;
        $pro->origin = $origin;
        $pro->accessory = $accessory;
        $pro->dimension = $dimension;
        $pro->weight = $weight;
        $pro->system = $system;
        $pro->material = $material;
        $pro->screen_size = $screen_size;
        $pro->wattage = $wattage;
        $pro->volume = $volume;
        $pro->resolution = $resolution;
        $pro->memory = $memory;
        if($from_day){
            $pro->percent = $percent;
            $pro->discount_price = $price - ($price * ($percent / 100));
            $pro->created_at = str_replace('T',' ',$from_day);
            $pro->updated_at = str_replace('T',' ',$to_day);
        }


        $pro->save();

        $image = $request->image;
        if($image){
            $tt = ltrim($image,'"[');
            $pp = rtrim($tt,'"]');
            $arr = explode('","', $pp);
            for ($i=0; $i < count($arr); $i++) { 
                $bt = ltrim($arr[$i],'"');
                $bp = rtrim($bt,'"');
                $pro_img = new Product_image;
                $pro_img->prodetail_id = $id1;
                $pro_img->image = $bp;
                $pro_img->created_at = $timedt;
                $pro_img->save();
                
            }
        }

        
            $sp = Product_detail::where('prodetail_id',$id1)->first();
                date_default_timezone_set('Asia/Ho_Chi_Minh');
            $now = time();
        if($sp->created_at){
            // From time
            $ftime = $sp->created_at;
            $ftime = date_parse_from_format('Y-m-d H:i:s', $ftime);
            $ftime_stamp = mktime($ftime['hour'],$ftime['minute'],$ftime['second'],$ftime['month'],$ftime['day'],$ftime['year']);
            // To time
            $ttime = $sp->updated_at;
            $ttime = date_parse_from_format('Y-m-d H:i:s', $ttime);
            $ttime_stamp = mktime($ttime['hour'],$ttime['minute'],$ttime['second'],$ttime['month'],$ttime['day'],$ttime['year']);
            if($now >= $ftime_stamp && $now <= $ttime_stamp){
                $sp->status_discount = 1;
                $sp->save();
            }else{
                $sp->status_discount = 0;
                $sp->save();
            }
        }

        if ($pro_img) {
            return response()->json(['success' => 'Thêm sản phẩm thành công!']);  
        }
        else{
            return response()->json(['error' => 'Thêm thất bại']);
        }
    }


    public function updateProduct(Request $request){
        $prodetail_id = $request->prodetail_id;
        $price = $request->price;
        $color = $request->color;
        $quantity = $request->quantity;
        $size = $request->size;
        $status_discount = $request->status_discount;
        $percent = $request->percent;
        $discount_price = $request->discount_price;
        $origin = $request->origin;
        $accessory = $request->accessory;
        $dimension = $request->dimension;
        $weight = $request->weight;
        $system = $request->system;
        $material = $request->material;
        $screen_size = $request->screen_size;
        $wattage = $request->wattage;
        $volume = $request->volume;
        $resolution = $request->resolution;
        $memory = $request->memory;
        $from_day = $request->from_day;
        $to_day = $request->to_day;



        $pro = Product_detail::where('prodetail_id',$prodetail_id)->first();
        $pro->price = $price;
        $pro->color = $color;
        $pro->quantity = $quantity;
        $pro->size = $size;
        $pro->status_discount = $status_discount;
        $pro->status_confirm = 0;
        $pro->percent = $percent;
        $pro->discount_price = $discount_price;
        $pro->origin = $origin;
        $pro->accessory = $accessory;
        $pro->dimension = $dimension;
        $pro->weight = $weight;
        $pro->system = $system;
        $pro->material = $material;
        $pro->screen_size = $screen_size;
        $pro->wattage = $wattage;
        $pro->volume = $volume;
        $pro->resolution = $resolution;
        $pro->memory = $memory;
        if($from_day){
            $pro->percent = $percent;
            $pro->discount_price = $price - ($price * ($percent / 100));
            $pro->created_at = str_replace('T',' ',$from_day);
            $pro->updated_at = str_replace('T',' ',$to_day);
        }

        $pro->save();

        $image = $request->image;
        if($image){
            $tt = ltrim($image,'"[');
            $pp = rtrim($tt,'"]');
            $arr = explode('","', $pp);
            for ($i=0; $i < count($arr); $i++) { 
                $bt = ltrim($arr[$i],'"');
                $bp = rtrim($bt,'"');
                $pro_img = new Product_image;
                $pro_img->prodetail_id = $id1;
                $pro_img->image = $bp;
                $pro_img->created_at = $timedt;
                $pro_img->save();
                
            }
        }

        $sp = Product_detail::where('prodetail_id',$id1)->first();
                date_default_timezone_set('Asia/Ho_Chi_Minh');
            $now = time();
        if($sp->created_at){
            // From time
            $ftime = $sp->created_at;
            $ftime = date_parse_from_format('Y-m-d H:i:s', $ftime);
            $ftime_stamp = mktime($ftime['hour'],$ftime['minute'],$ftime['second'],$ftime['month'],$ftime['day'],$ftime['year']);
            // To time
            $ttime = $sp->updated_at;
            $ttime = date_parse_from_format('Y-m-d H:i:s', $ttime);
            $ttime_stamp = mktime($ttime['hour'],$ttime['minute'],$ttime['second'],$ttime['month'],$ttime['day'],$ttime['year']);
            if($now >= $ftime_stamp && $now <= $ttime_stamp){
                $sp->status_discount = 1;
                $sp->save();
            }else{
                $sp->status_discount = 0;
                $sp->save();
            }
        }

        if ($pro_img) {
            return response()->json(['success' => 'Thêm sản phẩm thành công!']);  
        }
        else{
            return response()->json(['error' => 'Thêm thất bại']);
        }


    }


    public function deleteProduct(Request $request){
        $prodetail_id = $request->prodetail_id;

        $product = Product_detail::where('prodetail_id',$prodetail_id)->get();
        $pro = Product_detail::where('product_id',$product[0]->product_id)->get();
        if(count($pro) == 1){
            $pro2 = Product::where('product_id',$product[0]->product_id)->delete();
        }
        else{
            $pro3 = Product_detail::where('prodetail_id',$prodetail_id)->delete(); 
        }
        
        if ($pro) {
            return response()->json(['success' => 'Xóa sản phẩm thành công!']);  
        }
        else{
            return response()->json(['error' => 'Xóa sản phẩm thất bại']);
        }        
    }


    public function test(){
        // $id = $request->username;
        // // tính total theo shop -> duyệt đơn hàng
        // // $or = Order::find($id);
        // // $tong = 0;
        // // $arr = json_decode($or->order_detail);
        // // for ($i=0; $i < count($arr); $i++) { 
        // //     $tong += $arr[$i]->price * $arr[$i]->cart_quantity;
        // // }
        // // return $arr;
        // $shop = Shop::where('shop_id',$id)->first();
        //$fofrmat = now()->timezone('Asia/Ho_Chi_Minh')->format('Y\-m\-d\ h:i A');
        // $kq = "2020-10-20T13:06";
        // $tt = str_replace('T',' ',$kq);
        // $oo = now()->timezone('Asia/Ho_Chi_Minh');
        
        //     $sp = Product_detail::where('prodetail_id',$id1)->first();
        //         date_default_timezone_set('Asia/Ho_Chi_Minh');
        //     $now = time();
        //     // From time
        // if($sp->created_at){
        //     $ftime = $sp->created_at;
        //     $ftime = date_parse_from_format('Y-m-d H:i:s', $ftime);
        //     $ftime_stamp = mktime($ftime['hour'],$ftime['minute'],$ftime['second'],$ftime['month'],$ftime['day'],$ftime['year']);
        //     // To time
        //     $ttime = $sp->updated_at;
        //     $ttime = date_parse_from_format('Y-m-d H:i:s', $ttime);
        //     $ttime_stamp = mktime($ttime['hour'],$ttime['minute'],$ttime['second'],$ttime['month'],$ttime['day'],$ttime['year']);
        //     if($now >= $ftime_stamp && $now <= $ttime_stamp){
        //         $sp->status_discount = 1;
        //         $sp->save();
        //     }else{
        //         $sp->status_discount = 0;
        //         $sp->save();
        //     }
        // }
        

        $product = Product::all();
        return response()->json($product); 

    }



    
    
}

