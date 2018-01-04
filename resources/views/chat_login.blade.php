<!DOCTYPE html>
<html>
<head>
</head>
<body>

<form action="{{config('chat_const')['url']}}/chat/login" method="post">
    id : <input type="text" name="id"><br>
    pswd : <input type="text" name="pswd"><br>
    <input type="submit" name="init" value="로그인">
    {{ csrf_field() }}
</form><br>
</body>
</html>
