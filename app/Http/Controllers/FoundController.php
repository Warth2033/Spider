<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Found;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
//        判断是否传入了user_name
        if (!is_null($request->user_name)) {
            $user = User::select('user_id')
                ->where('user_name', $request->user_name)
                ->get();
            $user_id = $user[0]->user_id;
            $founds = Found::where('user_id', $user_id)->get();
        } else {
            $founds = Found::where([
                ['found_type', 'like', '%' . $request->found_type . "%"],
                ['found_comment', 'like', '%' . $request->found_comment . '%'],
                ['found_location', 'like', '%' . $request->found_location . '%'],
                ['found_time', 'like', '%' . $request->found_time . '%'],
//                ['user_id', '=', $user_id]
            ])->get();
        }
        if (empty($founds)) {
            return response()->json('未查询到寻主启示信息', 404);
        } else {
//          img_prefix 加上 found_img 才能构成一个完整的图片Url，但是默认使用80端口，
//          如果是其他端口要另加，这个我不想处理了，留给前端吧
            $founds->put('img_prefix', 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"] . '/storage/');
            return response()->json($founds, 200);
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
     * 保存一个新的资源到磁盘
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fileCharater = $request->file('found_img');
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
        $founds = Found::insert([
            'found_type' => $request->found_type,
            'found_comment' => $request->found_comment,
            'found_img' => $filename,
            'found_contact' => $request->found_contact,
            'found_location' => $request->found_location,
            'found_time' => $request->found_time,
            'user_id' => $request->user_id
        ]);
        if ($founds) {
            return response()->json('插入成功', 201);
        } else {
            return response()->json('插入失败', 422);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $found_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $found_id)
    {
        //img不变
//        dd($request->input('img_action'));
        if ($request->input('img_action') == '0') {
            $founds = Found::where('found_id', $found_id)->update([
                'found_type' => $request->found_type,
                'found_comment' => $request->found_comment,
                'found_contact' => $request->found_contact,
                'found_location' => $request->found_location,
                'found_time' => $request->found_time
            ]);
        }
        //删除img
        elseif ($request->input('img_action') == '1') {
            Storage::disk('public')->delete($request->img_old);
            $founds = Found::where('found_id', $found_id)->update([
                'found_type' => $request->found_type,
                'found_comment' => $request->found_comment,
                'found_img' => null,
                'found_contact' => $request->found_contact,
                'found_location' => $request->found_location,
                'found_time' => $request->found_time
            ]);
        }
        //更新img
        else {
            $fileCharater = $request->file('found_img');
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
            $founds = Found::where('found_id', $found_id)->update([
                'found_type' => $request->found_type,
                'found_comment' => $request->found_comment,
                'found_img' => $filename,
                'found_contact' => $request->found_contact,
                'found_location' => $request->found_location,
                'found_time' => $request->found_time
            ]);
        }

            if ($founds) {
                return response()->json('修改成功', 201);
            } else {
                return response()->json('修改失败', 400);
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $found_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($found_id)
    {
        //查询出found_img并删除
        $found_img = (Found::select('found_img')->where('found_id', '=', $found_id)->get())[0]->found_img;
        Storage::disk('public')->delete($found_img);
        //删除数据库记录
        $founds = Found::where('found_id', $found_id)->delete();
        if ($founds) {
            return response()->json('删除成功', 204);
        } else {
            return response()->json('删除失败', 404);
        }
    }
}
