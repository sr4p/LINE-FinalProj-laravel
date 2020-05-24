<!DOCTYPE html>
<html style="height: 100%;" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>MANAGEMENT LINEBOT LOGIN PAGE</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

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

<body style="text-align: center;background: rgb(34,193,195);background: linear-gradient(0deg, rgba(34,193,195,1) 10%, rgba(255,210,114,1) 100%);">
  <form action="/login" method="post" onsubmit="submit.disabled = true; return true;" class="needs-validation" style="width: 100%;height:100%;max-width: 330px;padding: 5px;margin: auto;display: block;" novalidate>@csrf
        <div class="card" style="width: 330px;height: 430px;top: 50%;left: 50%;position: absolute;transform: translate(-50%,-50%);border: 2px solid #ffb84d;border-radius: 25px; ">
  <div class="card-header" style="border-radius: 25px 25px 0px 0px;">
  <img style="border-radius: 35%;background-color: #ffdd99" src="https://img.icons8.com/color/96/000000/admin-settings-male.png"/><span style="font-size: 17px;" id="usernameShow"></span>
  <span ><h4 class="mt-3">LOGIN</h4></span>
  </div>
  <div class="card-body pt-4 pb-2">

  @if(session()->has('message'))
    <div class="" style="color:red;text-align: center;margin-top:-15px;">
      {{ session()->get('message') }}
    </div>
  @endif

  <div class="input-group mb-4 mt-2">
    <div class="input-group-prepend">
      <div class="input-group-text" style="border-radius: 25px 0px 0px 25px;"><img src="https://img.icons8.com/material-sharp/20/000000/user.png"/></div>
    </div>
    <input type="text" class="form-control" id="uname" name="userN" placeholder="ชื่อบัญชีผู้ใช้" style="border-radius: 0px 25px 25px 0px;" required>

    <div class="invalid-feedback">
          กรุณากรอกชื่อบัญชีผู้ใช้งาน
        </div>
  </div>

  <div class="input-group mb-2">
    <div class="input-group-prepend">
      <div class="input-group-text" style="border-radius: 25px 0px 0px 25px;"><img src="https://img.icons8.com/android/20/000000/key.png"/></div>
    </div>
    <input type="password" class="form-control" placeholder="รหัสผ่าน" id="pword" name="passW" style="border-radius: 0px 25px 25px 0px;" required>
    <div class="invalid-feedback">
          กรุณากรอกรหัสผ่านผู้ใช้งาน
        </div>
  </div>

  <a style="font-size: 12px;float:right;margin-top:5px" href="https://myid.buu.ac.th/recovery">ลืมรหัสผ่าน?</a>
  </div>
  <div class="card-footer text-muted" style="border-radius: 0px 0px 25px 25px;">
  <button class="btn btn-md btn-primary btn-block p-2" type="submit" name="submit" style="border-radius: 25px;" onclick="setLocal()">เข้าสู่ระบบ</button>
  </div>
</div>


          <!-- <div class="wrap-input100 validate-input m-b-16" data-validate="Username is required">
            <input class="input100" id="uname" type="text" name="userN" placeholder="ชื่อผู้ใช้">
          </div> -->

          <!-- <div class="wrap-input100 validate-input m-b-16" data-validate="Password is required">
            <input class="input100" id="pword" type="password" name="passW" autocomplete="off" placeholder="รหัสผ่าน">
          </div> -->

          

          <!-- <input type="submit" value="เข้าสู่ระบบ" name="submit" id="smit" class="login100-form-btn" style="cursor:pointer; margin-top:20px;" onclick="setLocal()"> -->
        
        
  </form>
</body>
</html>