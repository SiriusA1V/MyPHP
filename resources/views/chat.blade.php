<!DOCTYPE html>
<html>
<head>
    <script
            src="http://code.jquery.com/jquery-1.12.4.min.js"
            integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
            crossorigin="anonymous"></script>

    <script>

        $(document).ready(function () {
            $("#bt").click(function () {
                $.ajax({
                    type:"post",
                    url:"{{config('chat_const')['url']}}/chat/chat",
                    data:{
                        r_id:"{{$r_id}}",
                        contents:$("#tx").val(),
                        "_token": $('#token').val()
                    }
                }).done(function (data) {
                    replay();
                });
            });
        });


        function replay() {
            $.ajax({
                type:"get",
                url:"{{config('chat_const')['url']}}/chat/getChat_si",
                data:{
                    r_id:"{{$r_id}}"
                }
            }).done(function (data) {
                console.log("fuck");
                $("#divid").empty();
                $("#divid").append(data);
                $("#divid").scrollTop($("#divid")[0].scrollHeight);
            });
        }

        function setI() {
            setInterval(replay, 1000);
        }
    </script>


</head>
<body onload="setI()">

<div id="divid" style="border: 5px; height: 600px; width: 600px; word-break: break-all; overflow-y: scroll">
</div>


<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
<input type="button" id="bt" value="보내기">
<input type="text" id="tx" style="width:400px">

<br>
<br>
<form action="{{config('chat_const')['url']}}/chat/r_out" method="get">
    <input type="hidden" name="r_id" value="{{$r_id}}">
    <input type="submit" value="나가기">
</form>


</body>
</html>
