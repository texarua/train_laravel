<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//call model
use App\Models\Product;
use App\Models\User;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Intervention\Image\Facades\Image as Image;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\api\ProductAddRequest;
use App\Http\Requests\api\productEditRequest;
class ProductController extends Controller
{
    public $successStatus = 200;

    //
    public function productHome(){
        $getProductHome = Product::orderBy('id')->limit(6)->get()->toArray();
        return response()->json([
            'response' => 'success',
            'data' => $getProductHome
        ], $this->successStatus);
    }
    //
    public function productWishlist() {

        $getAllProduct = Product::all()->toArray();
        return response()->json([
            'response' => 'success',
            'data' => $getAllProduct
        ], $this->successStatus);
    }
    // porduct list
    public function productList() {
        $getAllProduct = Product::paginate(6);
        return response()->json([
            'response' => 'success',
            'data' => $getAllProduct
        ], $this->successStatus);
    }
    // product cart
    public function productCart(Request $request) {

        $data = $request->json()->all();

        $getProduct = [];
        foreach ($data as $key => $value) {
            $get = Product::findOrFail($key)->toArray();
            $get['qty'] = $value;
            $getProduct[] = $get;
        }
        return response()->json([
            'response' => 'success',
            'data' => $getProduct
        ], $this->successStatus);
    }

    public function myProduct(){
        $getAllProductUser = Product::all()->where('id_user', Auth::user()->id)->toArray();
        return response()->json([
            'response' => 'success',
            'data' => $getAllProductUser
        ], $this->successStatus);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listProduct()
    {
        $getAllProduct = Product::all()->toArray();
        return view('admin.product.list', compact('getAllProduct'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $data = Product::findOrFail($id);
        return response()->json([
            'response' => 'success',
            'data' => $data
        ], $this->successStatus);

    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Product::findOrFail($id);
        $data['image'] = json_decode($data['image'], true);
        return response()->json([
            'response' => 'success',
            'data' => $data
        ], $this->successStatus);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activeProduct($id)
    {

        $activeProduct = Product::find($id);
        $activeProduct->active = 1;
        $activeProduct->save();
        return redirect('/admin/product/list')->with('updated','Active product successfully.');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct($id)
    {

        $getProduct = Product::find($id)->toArray();
        $arrImage = json_decode($getProduct['image'], true);

        if (Product::find($id)->delete()) {
            foreach ($arrImage as $value) {
                if (file_exists('upload/user/product/'.Auth::user()->id.'/'.$value)) {

                    unlink('upload/user/product/'.Auth::user()->id.'/'.$value);
                    unlink('upload/user/product/'.Auth::user()->id.'/small_'.$value);
                    unlink('upload/user/product/'.Auth::user()->id.'/larger_'.$value);
                }
            }

            $getAllProductUser = Product::all()->where('id_user', Auth::user()->id)->toArray();
            return response()->json([
                'response' => 'success',
                'data' => $getAllProductUser
            ], $this->successStatus);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function listCategoryBrand() {
        $Category = Category::all()->toArray();
        $Brand = Brand::all()->toArray();

        return response()->json([
            'message' => 'success',
            'category' => $Category,
            'brand' => $Brand
        ], JsonResponse::HTTP_OK);
    }

    // create product
    public function store(ProductAddRequest $request) {

        $data = $request->all();
        if($request->hasfile('file')){
            $imageUpload = $this->uploadImageToDirectory($request->file('file'));
            $data['image']= json_encode($imageUpload);
        }

        $data['id_category'] = $data['category'];
        $data['id_brand'] = $data['brand'];
        $data['company_profile'] = $data['company'];
        $data['id_user'] = Auth::User()->id;

        if ($product = Product::create($data)) {
            // if($request->hasfile('file')){
            //     $this->uploadImageToDirectory($request->file('file'));
            // }
            return response()->json([
                'response' => 'success',
                'data' => $product
            ], $this->successStatus);
        }
    }

    // update product
    public function update(productEditRequest $request, $id) {

        $product = Product::findOrFail($id);
        $data = $request->all();

        // get json image convert to array and check remove
        $imageProduct = json_decode($product['image'], true);
        if(!empty($data['avatarCheckBox'])) {
            foreach($imageProduct as $key => $value) {
                if (in_array($value, $data['avatarCheckBox'])) {
                    if (file_exists('upload/user/product/'.Auth::user()->id.'/'.$value)) {

                        unlink('upload/user/product/'.Auth::user()->id.'/'.$value);
                        unlink('upload/user/product/'.Auth::user()->id.'/small_'.$value);
                        unlink('upload/user/product/'.Auth::user()->id.'/larger_'.$value);
                    }

                    unset($imageProduct[$key]);
                }
            }
            $imageProduct = array_values($imageProduct);
        }

        // get name upload file and merge with avatar old
        $imageMerge = !empty($imageProduct) ? $imageProduct : '';


        if($request->hasfile('file')){
            $imageUpload = $this->uploadImageToDirectory($request->file('file'));
            $imageMerge = array_merge($imageUpload, $imageProduct);
        }
        //

        if(count($imageMerge) > 5) {
            return response()->json([
                'message' => 'avatar only upload maximun 5 file',
            ], $this->successStatus);
        }


        $data = $request->all();

        $data['image'] = json_encode($imageMerge);
        $data['id_category'] = $data['category'];
        $data['id_brand'] = $data['brand'];
        $data['company_profile'] = $data['category'];
        $data['id_user'] = Auth::User()->id;

        if ($product->update($data)) {
            // save image in folder
            if($request->hasfile('file')){
                $imageUpload = $this->uploadImageToDirectory($request->file('file'));
            }

            return response()->json([
                'response' => 'success',
                'data' => $product
            ], $this->successStatus);
        }
    }


    public function uploadImageToDirectory($arrImage)
    {
        $ImageUpload = [];
        foreach($arrImage as $image){
            $nameImg = strtotime(date('Y-m-d H:i:s')).'_'.$image->getClientOriginalName();
            if (!file_exists('upload/user/product/'.Auth::user()->id)) {
                mkdir('upload/user/product/'.Auth::user()->id);
            }
            $path = public_path('upload/user/product/'.Auth::user()->id.'/'. $nameImg);
            $pathSmall = public_path('upload/user/product/'.Auth::user()->id.'/small_'.$nameImg);
            $pathLarger = public_path('upload/user/product/'.Auth::user()->id.'/larger_'.$nameImg);

            Image::make($image->getRealPath())->save($path);
            Image::make($image->getRealPath())->resize(84, 84)->save($pathSmall);
            Image::make($image->getRealPath())->resize(330, 380)->save($pathLarger);

            $ImageUpload[] = $nameImg;
        }
        return $ImageUpload;
    }

}
