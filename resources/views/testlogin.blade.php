<!DOCTYPE html>
<html style="height: 100%;" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Login</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

   <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2.0/sdk.js"></script>
    <script src="{{ asset('js/liff-starter.js')}}"></script>
    <script src="{{asset('js/liff-starter.js')}}"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        function getId(){
            liff.init({liffId:"1653845388-2mPZP8OR"}, () => {}, err => console.error(err.code, error.message));
        liff.getProfile().then(profile => {
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

(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
    </script>
  </head>
<body style="border: 1px solid black;height: 100%;background-color: #b3b3b3;text-align: center;"  onload="getId()">
<form id="frm" action='/line/registerBot' method='post' onsubmit="submit.disabled = true; return true;" class="needs-validation" style="border: 1px solid black;align-items: center;max-width: 330px;
  margin: 0 auto;" novalidate >
@csrf

<div class="card" style="margin-top:25%;margin-bottom:25%">
<input type= "hidden" name="u1" id="u1">
          <input type= "hidden" name="u2" id="u2">
          <input type= "hidden" name="u3" id="u3">
  <div class="card-header">
  <img style="border-radius: 50%;background-color: #ffdd99" src="https://img.icons8.com/color/96/000000/group.png"/><span style="font-size: 17px;" id="usernameShow"></span>
  <span ><h4 class="mt-3">LINEBOT LOGIN</h4></span>
  </div>
  <div class="card-body pt-4 pb-2">

  @if(session()->has('message'))
  <div class=" alert alert-danger mt-0 mb-0" style="color:red;text-align: center;">
  {{ session()->get('message') }}
    </div><br>
  @endif

  <div class="input-group mb-4">
    <div class="input-group-prepend">
      <div class="input-group-text"><img src="https://img.icons8.com/material-sharp/20/000000/user.png"/></div>
    </div>
    <input type="text" class="form-control" id="uname" name="userN" placeholder="Username" required>
    <div class="invalid-feedback">
          กรุณากรอกชื่อผู้ใช้งาน
        </div>
  </div>

  <div class="input-group mb-2">
    <div class="input-group-prepend">
      <div class="input-group-text"><img src="https://img.icons8.com/android/20/000000/key.png"/></div>
    </div>
    <input type="password" class="form-control" placeholder="Password" id="pword" name="passW" required>
    <div class="invalid-feedback">
          กรุณากรอกรหัสผ่านผู้ใช้งาน
        </div>
  </div>

  <a style="font-size: 12px;float:right" href="https://myid.buu.ac.th/recovery">Forgot Password?</a>
  </div>
  <div class="card-footer text-muted">
  <button class="btn btn-md btn-primary btn-block p-2" type="submit" name="submit">Sign in</button>
  </div>
</div>

</form>
  </body>
</html>
