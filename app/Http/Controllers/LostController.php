<?php

namespace App\Http\Controllers;

use App\Models\Found;
use Faker\Provider\Image;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\Lost;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

//            ->orWhere('user_id', $user_id)
//            ->get();

//        判断是否传入了user_name
        if (!is_null($request->user_name)) {
            $user = User::select('user_id')
                ->where('user_name', $request->user_name)
                ->get();
            $user_id = $user[0]->user_id;
            $losts = Lost::where('user_id', $user_id)->get();
        } else {
            $losts = Lost::where([
            ['lost_type', 'like', '%' . $request->lost_type . "%"],
            ['lost_comment', 'like', '%' . $request->lost_comment . '%'],
            ['lost_location', 'like', '%' . $request->lost_location . '%'],
            ['lost_time', 'like', '%' . $request->lost_time . '%'],
//                ['user_id', '=', $user_id]
        ])->get();
        }
        if (empty($losts)) {
            return response()->json('未查询到寻主启示信息', 404);
        } else {
//          img_prefix 加上 lost_img 才能构成一个完整的图片Url，但是默认使用80端口，
//          如果是其他端口要另加，这个我不想处理了，留给前端吧
            $losts->put('img_prefix', 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"] . '/storage/');
            return response()->json($losts, 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fileCharater = $request->file('lost_img');
        if ($fileCharater->isValid()) {
            //获取文件的扩展名
            $ext = $fileCharater->getClientOriginalExtension();

            //获取文件的绝对路径
            $path = $fileCharater->getRealPath();

            //定义文件名
            $filename = 'img/' . date('Y-m-d-h-i-s') . '.' . $ext;

            //存储文件。disk里面的public。总的来说，就是调用disk模块里的public配置
            Storage::disk('public')->put($filename, file_get_contents($path));
        }
        $losts = Lost::insert([
            'lost_type' => $request->lost_type,
            'lost_comment' => $request->lost_comment,
            'lost_img' => $filename,
            'lost_contact' => $request->lost_contact,
            'lost_location' => $request->lost_location,
            'lost_time' => $request->lost_time,
            'user_id' => $request->user_id
        ]);
        if ($losts) {
            return response()->json('插入成功', 201);
        } else {
            return response()->json('插入失败', 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $lost_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $lost_id)
    {
        //img不变
        if ($request->input('img_action') == '0') {
            $losts = ost::where('lost_id', $lost_id)->update([
                'lost_type' => $request->lost_type,
                'lost_comment' => $request->lost_comment,
                'lost_contact' => $request->lost_contact,
                'lost_location' => $request->lost_location,
                'lost_time' => $request->lost_time
            ]);
        }
        //删除img
        elseif ($request->input('img_action') == '1') {
            Storage::disk('public')->delete($request->img_old);
            $losts = Lost::where('lost_id', $lost_id)->update([
                'lost_type' => $request->lost_type,
                'lost_comment' => $request->lost_comment,
                'lost_img' => null,
                'lost_contact' => $request->lost_contact,
                'lost_location' => $request->lost_location,
                'lost_time' => $request->lost_time
            ]);
        }
        //更新img
        else {
            $fileCharater = $request->file('lost_img');
            if ($fileCharater->isValid()) {
                //获取文件的扩展名
                $ext = $fileCharater->getClientOriginalExtension();

                //获取文件的绝对路径
                $path = $fileCharater->getRealPath();

                //定义文件名
                $filename = 'img/' . date('Y-m-d-h-i-s') . '.' . $ext;

                //存储文件。disk里面的public。总的来说，就是调用disk模块里的public配置
                Storage::disk('public')->put($filename, file_get_contents($path));
            }
            //删除之前的img
            Storage::disk('public')->delete($request->img_old);
            $losts = Lost::where('lost_id', $lost_id)->update([
                'lost_type' => $request->lost_type,
                'lost_comment' => $request->lost_comment,
                'lost_img' => $filename,
                'lost_contact' => $request->lost_contact,
                'lost_location' => $request->lost_location,
                'lost_time' => $request->lost_time
            ]);
        }

        if ($losts) {
            return response()->json('修改成功', 201);
        } else {
            return response()->json('修改失败', 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $lost_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($lost_id)
    {
        //查询出found_img并删除
        $lost_img = (Lost::select('lost_img')->where('lost_id', '=', $lost_id)->get())[0]->lost_img;
        Storage::disk('public')->delete($lost_img);
        //删除数据库记录
        $losts = Lost::where('lost_id', $lost_id)->delete();
        if($losts) {
            return response()->json('删除成功', 204);
        }
        else {
            return response()->json('删除失败', 404);
        }
    }
}
