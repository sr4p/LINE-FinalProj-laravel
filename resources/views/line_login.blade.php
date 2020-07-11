<!DOCTYPE html>
<html style="height: 100%;" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LINEBOT LOGIN PAGE</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <link rel="stylesheet" href="{{asset('css/main.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('css/util.css')}}">
   <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2/sdk.js"></script>
    <script src="{{ asset('js/liff-starter.js')}}"></script>
    <!-- <script src="{{asset('js/liff-starter.js')}}"></script> -->
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> -->
  <!-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->
  <script src="https://code.jquery.com/jquery-3.5.0.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
    

// const pro_id,pro_pic,pro_name;
function getId(){
    const liffId = '1653845388-2mPZP8OR';
  liff.init({ liffId })
    liff.ready.then(() => {
      liff.getProfile().then(profile => {
            document.getElementById('u1').value = profile.userId;
            document.getElementById('u2').value = profile.pictureUrl;
            document.getElementById('u3').value = profile.displayName;
            pro_id= profile.userId;
            pro_pic= profile.pictureUrl;
            pro_name= profile.displayName;

    }).catch((err) => {
      console.log('error', err);
        });
})
    }

    </script>
    <style>
    loginbox {width: 350px;
              height: 500px;
              top: 50%;
              left: 50%;
              position: absolute;
              transform: translate(-50%,-50%);
              }
    </style>

  </head>
<body style="text-align: center;background: rgb(34,193,195);background: linear-gradient(0deg, rgba(34,193,195,1) 10%, rgba(255,210,114,1) 100%);"  onload="getId()">

<script type="text/javascript">

function close_login(){
    const liffId = '1653845388-2mPZP8OR';
  liff.init({ liffId })
    liff.ready.then(() => {
      liff.closeWindow();
})
    }

    $(document).ready(function() {
        $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $("#form_login").submit(function(e){
        e.preventDefault();
        var userN = $('input[name="userN"]').val();
        var passW = $('input[name="passW"]').val();
        var u1 = $("#u1").val();
        var u2 = $("#u2").val();
        var u3 = $("#u3").val();
        $.ajax({
            type: 'POST',
            url: '/line/registerBot',
            data: {
              userN: userN,
              passW: passW,
              u1:u1,
              u2:u2,
              u3:u3
            },
            success: function(data) {
                // $('#fail_login').hidden();
                if(data.success){
                  close_login();
                } else {
                  $('#fail_login').show();
                }

            }, error: function(data){
              $('#fail_login').show();
              alert("ERRRR login");
              
            }
        });
    });

    });
</script>
<!-- <body style="text-align: center;"  onload="getId()"> -->
<form id="form_login" action="/line/registerBot" method="post" class="needs-validation" style="width: 100%;height:100%;max-width: 330px;padding: 5px;margin: auto;display: block;">@csrf

<!-- <div class="card" style="width: 330px;height: 430px;top: 50%;left: 50%;position: absolute;transform: translate(-50%,-50%);border: 1px solid gray;border-radius: 25px; "> -->
<div class="card" style="width: 330px;height: 430px;top: 50%;left: 50%;position: absolute;transform: translate(-50%,-50%);border: 2px solid #ffb84d;border-radius: 25px; ">
          <input type= "hidden" name="u1" id="u1">
          <input type= "hidden" name="u2" id="u2">
          <input type= "hidden" name="u3" id="u3">
  <div class="card-header" style="border-radius: 25px 25px 0px 0px;">
  <img style="border-radius: 50%;background-color: #ffdd99" src="https://img.icons8.com/color/96/000000/group.png"/><span style="font-size: 17px;" id="usernameShow"></span>
  <span ><h4 class="mt-3">LINEBOT LOGIN</h4></span>
  </div>
  <div class="card-body pt-4 pb-2">

 
  <div id="fail_login" style="color:red;text-align: center;margin-top:-15px;display:none;"><p>กรุณาพิมพ์ไอดีหรือรหัสผ่านให้ถูกต้อง</p></div>

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
    <input class="btn btn-md btn-primary btn-block p-2" type="submit" name="submit_login" id="submit_login" value="เข้าสู่ระบบ" style="border-radius: 25px;" />
  </div>
</div>

</form>
  </body>
</html>
