<!doctype html>
    <html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<div class="container">
    <div class="panel-heading">上传文件</div>
    <form class="form-horizontal" method="post" action="{{ action('FilesController@upload') }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <label for="file">选择文件</label>
        <input id="file" type="file" class="form-control" name="source" >
        <button type="submit" class="btn btn-primary">确定</button>
    </form>
</div>
    <script>upcdn.b0.upaiyun.com/libs/jquery/jquery-1.10.2.min.js</script>
</body>
</html>
