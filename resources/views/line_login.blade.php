<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Login</title>
    <link rel="stylesheet" href="{{asset('css/main.css')}}">
	  <link rel="stylesheet" type="text/css" href="{{asset('css/util.css')}}">

    <script src="https://static.line-scdn.net/liff/edge/2.0/sdk.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

    <script>
        function getId(){
            liff.init({liffId:"1653845388-2mPZP8OR"}, () => {}, err => console.error(err.code, error.message));
        liff.getProfile().then(profile => {
            // document.getElementById('useridfield').textContent = profile.userId;
            document.getElementById('u1').value = profile.userId;
            document.getElementById('u2').value = profile.pictureUrl;
            document.getElementById('u3').value = profile.displayName;
    }).catch((err) => {
      console.log('error', err);
        });
    }
    </script>

    <script>
      $('#frm').bind('submit', function (e) {
    var button = $('#submit');

    button.prop('disabled', true);

    var valid = true;   
    if (!valid) { 
        e.preventDefault();
        button.prop('disabled', false);
    }
});
    </script>
  </head>
  <body onload="getId()">
    <div class="limiter">
      <div class="container-login100">
        <div class="wrap-login100 p-t-50 p-b-90">
            <span class="login100-form-title p-b-51">เข้าสู่ระบบ</span>

<form id="frm" action='/line/registerBot' method='post'>
@csrf

            <div class="wrap-input100 validate-input m-b-16" data-validate="Username is required">
              <input class="input100" id="uname" type="text" name="userN" placeholder="รหัสประจำตัว">
            </div>
            
            <div class="wrap-input100 validate-input m-b-16" data-validate="Password is required">
              <input class="input100" id="pword" type="password" name="passW" autocomplete  ="off" placeholder="รหัสผ่าน">
          <input type= "hidden" name="u1" id="u1">
          <input type= "hidden" name="u2" id="u2">
          <input type= "hidden" name="u3" id="u3">
            </div>

            
            @if(session()->has('message'))
    <div class="alert alert-success" style="color:red;text-align: center;">
        {{ session()->get('message') }}
    </div><br>
@endif
           
            <input type="submit" value="เข้าสู่ระบบ" name="submit" id="smit" class="login100-form-btn" >
</form>
        </div>
      </div>
    </div>
  </body>
</html>
