<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Rate_Blog;
use App\Http\Requests\admin\BlogRequest;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image as Image;
use App\Http\Requests\api\CommentRequest;
use App\Http\Requests\api\RateBlogRequest;
use Validator;
// use Auth;
use App\Models\comment;
class BlogController extends Controller
{
    public $successStatus = 200;
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function list()
    {


        $getBlogListComment = Blog::with('comment')->paginate(config('admin.paginate'));

        // return view("xxx", compact('getBlogListComment'))

        return response()->json([
            'blog' => $getBlogListComment
        ]);

    }
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $getBlog = null;
        return view('admin.blog.create',compact('getBlog'));
    }


    public function create_success(BlogRequest $request)
    {

        $data = $request->all();
        $data['id_auth'] = Auth::id();
        $file = $request->image;
        if(!empty($file)){
             $duoiImage = $file->getClientOriginalExtension();
            $data['image'] = $data['title'].'.'.$duoiImage;
            $path = public_path('upload/Blog/image/' . $data['image']);
        }

        if (Blog::create($data)) {
            if(!empty($file)){
               Image::make($file->getRealPath())->resize(846, 387)->save($path);
            }
            return redirect('/admin/blog')->with('success', __('Create blog success.'));
        } else {
            return redirect()->back()->withErrors('Create blog error.');
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        if(!empty($id)) {

            // $getBlogDetail = Blog::with('comment')->find($id)->orderBy('comment.id', 'desc');

            $getBlogDetail = Blog::with(['comment' => function ($q) {
              $q->orderBy('id', 'desc');
            }])->find($id);

            return response()->json([
                'status' => 200,
                'data' => $getBlogDetail
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $getBlog = Blog::find($id)->toArray();
        return view('admin.blog.create', compact('getBlog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BlogRequest $request, $id)
    {

        $blog = Blog::findOrFail($id);

        $deleteImage = 'upload/Blog/image/'.$blog['image'];
        $data = $request->all();
        $data['id_auth'] = Auth::id();
        $file = $request->image;
        if(!empty($file)){
            $duoiImage = $file->getClientOriginalExtension();
            $data['image'] = $blog['title'].'.'.$duoiImage;
            $path = public_path('upload/Blog/image/' . $data['image']);
        }

        if ($blog->update($data)) {
            if(!empty($file)){
                if(!empty($deleteImage))
                {
                    unlink($deleteImage);
                }
               Image::make($file->getRealPath())->resize(846, 387)->save($path);

            }
            return redirect()->back()->with('success', __('Update profile success.'));
        } else {
            return redirect()->back()->withErrors('Update profile error.');
        }
    }
    public function delete($id)
    {
        $blog = DB::table('blog')->where('id',$id)->delete();
        if ($blog) {

            return redirect('/admin/blog')->with('success', 'Delete blog success.');
        } else {

            return redirect('/admin/blog')->withErrors('Delete blog error.');
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

    public function comment(CommentRequest $request, $id)
    {

        $data = $request->all();
        if ($id) {
            $getListComment = comment::create($data);
            if($getListComment){
                return response()->json([
                    'status' => 200,
                    'data' => $getListComment
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'error' => 'error'
                ]);
            }

        } else {
                return response()->json([
                    'status' => 200,
                    'error' => 'id not found'
                ]);
        }
    }

    public function pagingBlogDetail(Request $request, $id) {

        $blog = Blog::find($id);
        $previous = Blog::where('id', '<', $blog->id)->max('id');
        $next = Blog::where('id', '>', $blog->id)->min('id');

        if($blog){
            return response()->json([
                'status' => 200,
                'data' => $blog,
                'previous' => $previous,
                'next' => $next
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'error' => 'error'
            ]);
        }

    }
    // get all rate with id
    public function rateBlog($id)
    {
        $getAllRate = Rate_Blog::all()->where('blog_id', $id)->toArray();
        return response()->json([
            'response' => 'success',
            'data' => $getAllRate
        ], $this->successStatus);


        // $getAllRate = Rate_Blog::all()->where('blog_id', $id)->toArray();
        // return response()->json([
        //     'response' => 'success',
        //     'data' => $getAllRate
        // ], 200);
    }
    // post rate with id
    public function rate(RateBlogRequest $request)
    {
        $input = $request->all();
        if (!empty($input['user_id'])) {
            if (Rate_Blog::create($input)) {
                return response()->json([
                  'status' => 200,
                  'message' => 'You have rate this blog successfully.'
                ]);
            } else {
                return response()->json([
                  'status' => 200,
                  'message' => 'Lỗi server , bạn đánh giá k thành công.'
                ]);
            }
        }
    }

}
