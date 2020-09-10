<?php

namespace App\Http\Controllers;

use Composer\Package\Package;
use Illuminate\Http\Request;
use App\Models\User;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UsersController extends Controller
{
    /**
     * 获取所有用户的信息
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::where([
            ['user_name', '=', $request->user_name],
            ['pass', '=', $request->pass]
        ])->get();
        if(is_null($users)) {
            return response()->json('未找到资源', 404);
        }
        else {
            return response()->json($users, 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function create()
//    {
//        //
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new User();
        $user->user_name = $request->user_name;
        $user->pass = $request->pass;
        $user->college = $request->college;
        $user->class = $request->class;
        $user->email = $request->email;
        if($user->save()) {
            return response()->json('插入用户信息成功', 201);
        }
        else {
            return response()->json('插入用户信息失败', 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $name
     * @return \Illuminate\Http\Response
     */
    public function show($name)
    {
        $users = User::where('user_name', $name)->first();
        if(is_null($users)) {
            return response()->json('查询的信息不存在', 404);
        }
        else {
            return response()->json($users, 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
//    public function edit($id)
//    {
//        //
//    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $user_name
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_name)
    {
        $users = User::where('user_name', $user_name)->update([
                'user_name'=>$request->user_name,
                'pass'=>$request->pass,
                'college'=>$request->college,
                'class'=>$request->class,
                'email'=>$request->email,
                'limits'=>$request->limits
            ]);
        if($users) {
            return response()->json('修改成功', 201);
        }
        else {
            return response()->json('修改失败', 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $user_name
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_name)
    {
        $users = User::where('user_name', $user_name)->delete();
        if($users) {
            return response()->json('删除成功', 204);
        }
        else {
            return response()->json('删除失败', 404);
        }
    }
}
