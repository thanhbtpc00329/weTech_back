<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cloudder;
use App\Banner;

class AdminController extends Controller
{
    // Banner
    public function showBanner(){
        return view('upload');
    }
	public function addBanner(Request $request){
		$data=$request->all();
        echo $data['hinh'];
        // if ($request->hasFile('avatar')) {
        //     //get name image
            $filename = $data['hinh'];
            $name = $filename->getClientOriginalName();
            //upload image
            Cloudder::upload($name, 'products/' . $name);
         // }
         //get url image on Cloudinary
         // return Cloudder::show('uploads/'. $name);
	}

	public function updateBanner(Request $request)
    {
        $data=$request->all();
        $banner = Banner::find($data['num']);
        $banner->banner_name=$data['ten'];
        $banner->banner_description=$data['mota'];
        $banner->status=$data['op'];
        // $banner->updated_at = now()->timezone('Asia/Ho_Chi_Minh');
        $banner->save();
        if ($banner) {
            echo 'Thành công';
        }
        else{
            echo 'Lỗi';
        }
    }

	public function deleteBanner(Request $request)
    {
    	$data=$request->all();
        $banner = Banner::find($data['num']);
        if ($banner) {
            echo 'Thành công';
        }
        else{
            echo 'Lỗi';
        }
        $banner->delete();
    }
}