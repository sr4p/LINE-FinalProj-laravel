<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>เข้าสู่ระบบ</title>
  <link rel="stylesheet" href="{{asset('css/main.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('css/util.css')}}">
  <script src="https://static.line-scdn.net/liff/edge/2.0/sdk.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", get);

    function get() {
      $.ajax({
        type: "GET",
        url: 'checkLogin',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {

        }
      });
    }

    function setLocal() {
      localStorage.setItem("username", (document.getElementById('uname').value));
    }
  </script>

</head>

<body>
  <div class="limiter">
    <div class="container-login100">
      <div class="wrap-login100 p-t-50 p-b-90">
        <span class="login100-form-title p-b-51">เข้าสู่ระบบ</span>

        <form action="/login" method="post">@csrf
          <div class="wrap-input100 validate-input m-b-16" data-validate="Username is required">
            <input class="input100" id="uname" type="text" name="userN" placeholder="ชื่อผู้ใช้">
          </div>

          <div class="wrap-input100 validate-input m-b-16" data-validate="Password is required">
            <input class="input100" id="pword" type="password" name="passW" autocomplete="off" placeholder="รหัสผ่าน">
          </div>

          @if(session()->has('message'))
          <div class="alert alert-success" style="color:red; margin-bottom:15px;">
            {{ session()->get('message') }}
          </div>
          @endif

          <input type="submit" value="เข้าสู่ระบบ" name="submit" id="smit" class="login100-form-btn" style="cursor:pointer; margin-top:20px;" onclick="setLocal()">
        </form>
      </div>
    </div>
  </div>
</body>

</html>