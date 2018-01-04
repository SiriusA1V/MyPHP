<!DOCTYPE html>
<html>
<head>



</head>
<body>

<table border="1">
    <tr><td>번호</td><td>채팅방 이름</td><td>방장</td><td>인원</td><td>개설일자</td></tr>
    @for($i = 0; $i < sizeof($list); $i++)
        <tr>
            @for($j = 0; $j < sizeof($list[$i]); $j++)
                @if($j == 2)
                    <td><a href="{{config('chat_const')['url']}}/chat/room?r_id={{$list[$i][$j-1]}}">{{$list[$i][$j]}}</a></td>
                @elseif($j != 1)
                    <td>{{$list[$i][$j]}}</td>
                @endif
            @endfor
        </tr>
    @endfor
</table>

<a href='{{config('chat_const')['url']}}/chat/list?page={{$page[0]}}'><</a>
@for($i = 2; $i < sizeof($page); $i++)
    <a href='{{config('chat_const')['url']}}/chat/list?page={{$page[$i]}}'>{{$page[$i]}}</a>
@endfor
<a href='{{config('chat_const')['url']}}/chat/list?page={{$page[1]}}'>></a>

<br><br>
<form action="{{config('chat_const')['url']}}/chat/m_title" method="get">
    <input type="submit" value="방개설">
</form>
<br>
<form action="{{config('chat_const')['url']}}/chat/logout" method="get">
    <input type="submit" value="로그아웃">
</form>
</body>
</html>
