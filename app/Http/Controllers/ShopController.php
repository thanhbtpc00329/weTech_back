<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Shop;
use App\Order;
use App\Product_detail;
use App\Notification;
use DB; 


class ShopController extends Controller
{
    // Shop
    public function accountShopActive()
    {
        $shop = User::where('role','Member')->where('status',1)->paginate(10);
        return response()->json($shop);
    }

    public function accountShopUnactive()
    {
        $shop = User::where('role','Member')->where('status',0)->paginate(10);
        return response()->json($shop);
    }

    public function showShop(){
    	$shop = DB::table('shops')
    			->join('users','users.user_id','=','shops.user_id')
    			->paginate(10);
    	return response()->json($shop);
    }

    public function getShop(){
        $shop = DB::table('shops')
                ->join('users','users.user_id','=','shops.user_id')
                ->where('shops.status','=',1)
                ->paginate(10);
        return response()->json($shop);
    }  

    public function unactiveShop(){
        $shop = DB::table('shops')
                ->join('users','users.user_id','=','shops.user_id')
                ->where('shops.status','=',0)
                ->paginate(10);
        return response()->json($shop);
    }    

    public function blockShop(){
        $shop = DB::table('shops')
                ->join('users','users.user_id','=','shops.user_id')
                ->where('shops.status','=',2)
                ->paginate(10);
        return response()->json($shop);
    } 

    public function detailShop(Request $request){
        $shop_id = $request->id;

        $shop = DB::table('shops')
                ->join('users','users.user_id','=','shops.user_id')
                ->select('shops.shop_id','shops.shop_name','shops.shop_address','shops.phone_number','shops.background','shops.shop_range','shops.location','shops.tax','users.avatar')
                ->where('shop_id','=',$shop_id)
                ->get();
        return response()->json($shop);
    }


    public function rangeShop(Request $request){
        $shop_id = $request->shop_id;

        $shop = Shop::where('shop_id','=',$shop_id)->first();
        return response()->json($shop);
    }



    public function addShop(Request $request){
        $shop_name = $request->shop_name;
        $user_id = $request->user_id;
        $identity_card = $request->identity_card;
        $shop_address = $request->shop_address;
        $location = $request->location;
        $shop_range = $request->shop_range;
        $phone_number = $request->phone_number;
        $tax = $request->tax;

        $img = User::where('user_id',$user_id)->first();
        $img->avatar ='https://res.cloudinary.com/dtvapimtn/image/upload/v1604655912/users/cd68867179e387bddef2_m4luzo.jpg';
        $img->save();


        $shop = new Shop;
        $shop->shop_name = $shop_name;
        $shop->user_id = $user_id;
        $shop->identity_card = $identity_card;
        $shop->shop_address = $shop_address;
        $shop->location = $location;
        $shop->shop_range = $shop_range;
        $shop->phone_number = $phone_number;
        $shop->tax = $tax;
        $shop->background = 'https://res.cloudinary.com/dtvapimtn/image/upload/v1604657857/backgrounds/background_skzncs.jpg';
        $shop->status = '0';
        $shop->created_at = now()->timezone('Asia/Ho_Chi_Minh');

        $shop->save();

        
        if ($shop) {
            return response()->json(['success' => 'Đăng ký thành viên thành công!']);  
        }
        else{
            return response()->json(['error' => 'Đăng ký thành viên thất bại']);
        }  
    }

    public function activeShop(Request $request){
        $shop_id = $request->shop_id;
        $user_id = $request->user_id;
        $shop = Shop::where('shop_id',$shop_id)->update(['status' => 1]);
        $mem = User::where('user_id',$user_id)->update(['role' => 'Member']);
        if ($mem) {
            return response()->json(['success' => 'Kích hoạt thành viên thành công!']);  
        }
        else{
            return response()->json(['error' => 'Kích hoạt thành viên thất bại']);
        }
    }


    public function cancelShop(Request $request){
        $shop_id = $request->shop_id;
        $user_id = $request->user_id;
        $shop = Shop::where('shop_id',$shop_id)->update(['status' => 2]);
        $mem = User::where('user_id',$user_id)->update(['role' => 'User']);
        if ($mem) {
            return response()->json(['success' => 'Chặn thành viên thành công!']);  
        }
        else{
            return response()->json(['error' => 'Chặn thành viên thất bại']);
        }
    }


    public function updateShop(Request $request){
        $id = $request->shop_id;
        $name = $request->name;
        $shop_address = $request->shop_address;
        $location = $request->location;
        $shop_range = $request->shop_range;
        $phone_number = $request->phone_number;
        $status = $request->status;
        $tax = $request->tax;

        $shop = Shop::where('shop_id',$id)->update([
            'name'=>$name,
            'address'=>$address,
            'location'=>$location,
            'shop_range'=>$shop_range,
            'phone_number'=>$phone_number,
            'tax' => $tax,
            'status'=>$status,
            'updated_at'=>now()->timezone('Asia/Ho_Chi_Minh')
        ]);

        if ($shop) {
            return response()->json(['success' => 'Cập nhật thành công!']);  
        }
        else{
            return response()->json(['error' => 'Cập nhật thất bại']);
        }  
    }

    public function deleteShop(Request $request){
        $id = $request->shop_id;

        $shop = Shop::where('shop_id',$id)->delete();
        if ($shop) {
            return response()->json(['success' => 'Xóa thành công!']);  
        }
        else{
            return response()->json(['error' => 'Xóa thất bại']);
        }  
    }

    // Order
    public function updateOrder(Request $request){
        $id = $request->id; 

        $order = Order::find($id);
        $order->status = 'Đã đóng gói';
        $order->updated_at = now()->timezone('Asia/Ho_Chi_Minh');

        $order->save();
        if ($order) {
            return response()->json(['success' => 'Thành công!']);  
        }
        else{
            return response()->json(['error' => 'Lỗi']);
        }
    }


    // Order

    public function getOrderShop(Request $request){
        $shop_id = $request->shop_id;

        $order = DB::table('orders')
                ->join('users','users.user_id','=','orders.user_id')
                ->where('orders.shop_id','=',$shop_id)
                ->orderBy('orders.created_at','DESC')
                ->select('orders.id','orders.user_id','orders.order_address','orders.shipping','orders.total','orders.shop_id','orders.shipper_deliver','orders.shipper_receive','orders.status','orders.order_detail','orders.created_at','users.name','users.avatar','users.phone_number')
                ->paginate(5);
        return response()->json($order);
    }

    public function unactiveOrderShop(Request $request){
        $shop_id = $request->shop_id;

        $order = DB::table('orders')
                ->join('users','users.user_id','=','orders.user_id')
                ->where('orders.shop_id','=',$shop_id)
                ->where('orders.status','=','Chờ duyệt')
                ->orderBy('orders.created_at','DESC')
                ->select('orders.id','orders.user_id','orders.order_address','orders.shipping','orders.total','orders.shop_id','orders.shipper_deliver','orders.shipper_receive','orders.status','orders.order_detail','orders.created_at','users.name','users.avatar','users.phone_number')
                ->paginate(5);
        return response()->json($order);
    }

    public function activeOrderShop(Request $request){
        $shop_id = $request->shop_id;

        $order = DB::table('orders')
                ->join('users','users.user_id','=','orders.user_id')
                ->where('orders.shop_id','=',$shop_id)
                ->where('orders.status','=','Đã duyệt')
                ->orderBy('orders.created_at','DESC')
                ->select('orders.id','orders.user_id','orders.order_address','orders.shipping','orders.total','orders.shop_id','orders.shipper_deliver','orders.shipper_receive','orders.status','orders.order_detail','orders.created_at','users.name','users.avatar','users.phone_number')
                ->paginate(5);
        return response()->json($order);
    }

    public function updateOrderShop(Request $request){

        $shop_id = $request->shop_id;

        $order = DB::table('orders')
                ->join('users','users.user_id','=','orders.user_id')
                ->where('orders.shop_id','=',$shop_id)
                ->where('orders.status','=','Đã đóng gói')
                ->orderBy('orders.created_at','DESC')
                ->select('orders.id','orders.user_id','orders.order_address','orders.shipping','orders.total','orders.shop_id','orders.shipper_deliver','orders.shipper_receive','orders.status','orders.order_detail','orders.created_at','users.name','users.avatar','users.phone_number')
                ->paginate(5);
        return response()->json($order);
    }


    public function toWarehouse(Request $request){
        $shop_id = $request->shop_id;

        $order = DB::table('orders')
                ->join('users','users.user_id','=','orders.user_id')
                ->where('orders.shop_id','=',$shop_id)
                ->where(function($query) {
                    $query->orWhere('orders.status','=','Đang lấy hàng')
                          ->orWhere('orders.status','=','Đã lấy hàng')
                          ->orWhere('orders.status','=','Đã lấy hàng')
                          ->orWhere('orders.status','=','Đã nhập kho');
                })
                ->orderBy('orders.created_at','DESC')
                ->select('orders.id','orders.user_id','orders.order_address','orders.shipping','orders.total','orders.shop_id','orders.shipper_deliver','orders.shipper_receive','orders.status','orders.order_detail','orders.created_at','users.name','users.avatar','users.phone_number')
                ->paginate(5);
        return response()->json($order); 

    }


    public function confirmOrderShop(Request $request){
        
        $shop_id = $request->shop_id;

        $order = DB::table('orders')
                ->join('users','users.user_id','=','orders.user_id')
                ->where('orders.shop_id','=',$shop_id)
                ->where('orders.status','=','Đang giao')
                ->orderBy('orders.created_at','DESC')
                ->select('orders.id','orders.user_id','orders.order_address','orders.shipping','orders.total','orders.shop_id','orders.shipper_deliver','orders.shipper_receive','orders.status','orders.order_detail','orders.created_at','users.name','users.avatar','users.phone_number')
                ->paginate(5);
        return response()->json($order);
    }

    public function finishOrderShop(Request $request){

        $shop_id = $request->shop_id;

        $order = DB::table('orders')
                ->join('users','users.user_id','=','orders.user_id')
                ->where('orders.shop_id','=',$shop_id)
                ->where('orders.status','=','Đã giao')
                ->orderBy('orders.created_at','DESC')
                ->select('orders.id','orders.user_id','orders.order_address','orders.shipping','orders.total','orders.shop_id','orders.shipper_deliver','orders.shipper_receive','orders.status','orders.order_detail','orders.created_at','users.name','users.avatar','users.phone_number')
                ->paginate(5);
        return response()->json($order);
    }

    public function cancelOrderShop(Request $request){

        $shop_id = $request->shop_id;

        $order = DB::table('orders')
                ->join('users','users.user_id','=','orders.user_id')
                ->where('orders.shop_id','=',$shop_id)
                ->where('orders.status','=','Đã hủy')
                ->orderBy('orders.created_at','DESC')
                ->select('orders.id','orders.user_id','orders.order_address','orders.shipping','orders.total','orders.shop_id','orders.shipper_deliver','orders.shipper_receive','orders.status','orders.order_detail','orders.created_at','users.name','users.avatar','users.phone_number')
                ->paginate(5);
        return response()->json($order);
    }

    public function returnOrderShop(Request $request){

        $shop_id = $request->shop_id;

        $order = DB::table('orders')
                ->join('users','users.user_id','=','orders.user_id')
                ->where('orders.shop_id','=',$shop_id)
                ->where('orders.status','=','Trả hàng')
                ->orderBy('orders.created_at','DESC')
                ->select('orders.id','orders.user_id','orders.order_address','orders.shipping','orders.total','orders.shop_id','orders.shipper_deliver','orders.shipper_receive','orders.status','orders.order_detail','orders.created_at','users.name','users.avatar','users.phone_number')
                ->paginate(5);
        return response()->json($order);
    }

    public function insertOrderShop(Request $request){

        $shop_id = $request->shop_id;

        $order = DB::table('orders')
                ->join('users','users.user_id','=','orders.user_id')
                ->where('orders.shop_id','=',$shop_id)
                ->where('orders.status','=','Đã nhập kho')
                ->orderBy('orders.created_at','DESC')
                ->select('orders.id','orders.user_id','orders.order_address','orders.shipping','orders.total','orders.shop_id','orders.shipper_deliver','orders.shipper_receive','orders.status','orders.order_detail','orders.created_at','users.name','users.avatar','users.phone_number')
                ->paginate(5);
        return response()->json($order);
    }

    public function shopCheck(Request $request){
        $id = $request->id; 
        $total = $request->total;

        $order = Order::find($id);
        $order->status = 'Đã duyệt';
        $order->total = $total;
        $order->updated_at = now()->timezone('Asia/Ho_Chi_Minh');

        $order->save();
        if ($order) {
            return response()->json(['success' => 'Thành công!']);  
        }
        else{
            return response()->json(['error' => 'Lỗi']);
        }
    }

    public function shopUpdate(Request $request){
        $id = $request->id; 

        $order = Order::find($id);
        $order->status = 'Đã đóng gói';
        $order->updated_at = now()->timezone('Asia/Ho_Chi_Minh');

        $order->save();
        if ($order) {
            return response()->json(['success' => 'Thành công!']);  
        }
        else{
            return response()->json(['error' => 'Lỗi']);
        }
    }

    public function activeDiscount(Request $request)
    {
        $shop_id = $request->shop_id;

        $pro = DB::table('product_detail')
            ->join('products','products.product_id','=','product_detail.product_id')
            ->where('products.shop_id','=',$shop_id)
            ->select('product_detail.created_at','product_detail.updated_at')
            ->get();
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
        $prod2 = DB::table('products')
                ->join('product_detail','product_detail.product_id','=','products.product_id')
                ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
                ->groupBy('product_detail.product_id')
                ->where('products.shop_id','=',$shop_id)
                ->where('product_detail.status_discount','=',1)
                ->where('product_detail.status_confirm','=',1)
                ->select('products.product_id','products.product_name','products.brand','products.cate_id','product_detail.price','product_image.image')
                ->paginate(5);
        return response()->json($prod2);
    }

    public function unactiveDiscount(Request $request)
    {
        $shop_id = $request->shop_id;

        $pro = DB::table('product_detail')
            ->join('products','products.product_id','=','product_detail.product_id')
            ->where('products.shop_id','=',$shop_id)
            ->select('product_detail.created_at','product_detail.updated_at')
            ->get();
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
        $prod2 = DB::table('products')
                ->join('product_detail','product_detail.product_id','=','products.product_id')
                ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
                ->groupBy('product_detail.product_id')
                ->where('products.shop_id','=',$shop_id)
                ->where('product_detail.status_discount','=',0)
                ->where('product_detail.status_confirm','=',1)
                ->select('products.product_id','products.product_name','products.brand','products.cate_id','product_detail.price','product_image.image')
                ->get();
        return response()->json($prod2);
    }


    public function discount(Request $request)
    {
        $from_day = $request->from_day;
        $to_day = $request->to_day;
        $percent = $request->percent;
        $prodetail_id = $request->prodetail_id;
        $pp = ltrim($prodetail_id,"'");
        $kk = rtrim($pp,"'");
        $id = json_decode($kk);

            for ($i=0; $i < count($id); $i++) { 
                $pro = Product_detail::where('prodetail_id',$id[$i])->first();
                $price = $pro->price;
                $pro->percent = $percent;
                $pro->discount_price = $price - ($price * ($percent / 100));
                $pro->status_discount = 1;
                $pro->created_at = str_replace('T',' ',$from_day);
                $pro->updated_at = str_replace('T',' ',$to_day);
                $pro->save();
            }

        if ($pro) {
            return response()->json(['success' => 'Thành công!']);  
        }
        else{
            return response()->json(['error' => 'Thất bại']);
        }
    }


    // Count sp theo shop
    public function countProductShop(Request $request){
        $shop_id = $request->shop_id;

        $product = DB::table('product_detail')
            ->join('products','product_detail.product_id','=','products.product_id')
            ->where('products.shop_id','=',$shop_id)
            ->where('product_detail.status_confirm','=',1)
            ->count();
        return $product;
    }


    public function searchProductShop(Request $request)
    {
        $keywords = $request->keywords;
        $shop_id = $request->shop_id;

        $product = DB::table('products')
            ->join('product_detail','product_detail.product_id','=','products.product_id')
            ->join('product_image','product_image.prodetail_id','=','product_detail.prodetail_id')
            ->join('shops','shops.shop_id','=','products.shop_id')
            ->where('products.product_name','like','%'.$keywords.'%')
            ->where('product_detail.status_confirm','=',1)
            ->where('products.shop_id','=',$shop_id)
            ->groupBy('product_detail.product_id')
            ->paginate(5);
        return response()->json($product);
    }


    public function searchOrderShop(Request $request)
    {
        $keywords = $request->keywords;
        $shop_id = $request->shop_id;

        $product = Order::where('order_address','like','%'.$keywords.'%')->where('shop_id',$shop_id)->paginate(5);
        return response()->json($product);
    } 


    public function searchUnactiveOrderShop(Request $request)
    {
        $keywords = $request->keywords;
        $shop_id = $request->shop_id;

        $product = Order::where('order_address','like','%'.$keywords.'%')->where('shop_id',$shop_id)->where('status','Chờ duyệt')->paginate(5);
        return response()->json($product);
    } 







    // Notification
    public function addNotification(Request $request){
        $order_id = $request->order_id;
        $message = $request->message;
        $type = $request->type;

        $tb = new Notification;
        $tb->order_id = $order_id;
        $tb->message = $message;
        $tb->type = $type;
        $tb->created_at = now()->timezone('Asia/Ho_Chi_Minh');
        $tb->save();
        
        if ($tb) {
            return response()->json(['success' => 'Thành công!']);  
        }
        else{
            return response()->json(['error' => 'Thất bại']);
        }
    }

    public function deleteNotification(Request $request){
        $id = $request->id;

        $tb = Notification::find($id);
        $tb->delete();
        
        if ($tb) {
            return response()->json(['success' => 'Thành công!']);  
        }
        else{
            return response()->json(['error' => 'Thất bại']);
        }
    }


    public function notification(Request $request){
        $tb = Notification::all();

        return response()->json($tb);
    }


}
