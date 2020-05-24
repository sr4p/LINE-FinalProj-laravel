<?php

require_once '../vendor/autoload.php';
?>
<!DOCTYPE html>
<html lang="en,{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="{{asset('css/main.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('css/util.css')}}">
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <title>หน้าหลัก</title>

  <script>
    function ready() {
      var getUsername = localStorage.getItem("username");
      document.getElementById('usernameShow').innerHTML = getUsername;
    }
    document.addEventListener("DOMContentLoaded", ready);

    function removeUname() {
      $.ajax({
        type: "GET",
        url: 'logout',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function() {
          window.location.href = '/'
          console.log('logged out');
        }
      });
      localStorage.removeItem("username");
    }

    $(document).ready(function() {
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

      $("#btn-submit").click(function(e){
        console.log("111111")
        e.preventDefault();
        var accessToken = $("input[name=cat]").val();
        var Secret = $("input[name=cs]").val();

        $.ajax({
            type: 'POST',
            url: '/changeConfig',
            data: {
              "_token": "{{ csrf_token() }}",
                cat: accessToken,
                cs: Secret,
            },
            success: function(data) {
                window.location.reload();
            }
        });
});

$("#delNotification").click(function(e){
        e.preventDefault();
// alert("del")

        $.ajax({
            type: 'POST',
            data: {
              "_token": "{{ csrf_token() }}"
            },
            url: '/delNotification',
            success: function(data) {
                window.location.reload();
            },
        error: function(data) {
          console.log('No');
        }
        });
});


    });
  </script>
</head>

<body>

  <nav class="navbar navbar-expand-lg sticky-top navbar-light" style="background-color: rgb(226, 196, 123);">
    <div class="container">
      <a class="navbar-brand " href="/main">
      <img src="https://upload.wikimedia.org/wikipedia/commons/e/ec/Buu-logo11.png" width="50" height="50" class="" alt="">
      LINE CHATBOT BUU
      </a>
    </div>
    <div class="dropdown">
      <button class="outline-primary my-2 my-sm-0 dropdown-toggle" style="margin-left:55px;margin-right:50px" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
      <img src="https://img.icons8.com/ios-glyphs/30/000000/person-male.png"/><span style="font-size: 17px;" id="usernameShow"></span>
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
        <button class="dropdown-item" name="submit" type="button" onclick="removeUname()"><img src="https://img.icons8.com/ios-glyphs/20/000000/door-opened.png"/>ออกจากระบบ</button>
      </div>
    </div>
    </div>
  </nav>
  <div class="limiter">
    <div class="container-main">
      <div class="wrap-login100 p-t-20 p-b-10">
        <span class="text-form p-b-30 font-weight-bold" style="font-size: 21px;">ระบบสารสนเทศเพื่อจัดการ LINE CHATBOT</span>
        <span>
        @if(Session::has('success'))
    <div class="alert alert-success" style="width:auto;text-align: center;">
        {{Session::get('success')}}
    </div>
@endif
        </span>
      </div>

    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col">
        <div class="card">
          <h5 class="card-header" style="background-color: rgb(226, 196, 123);">จัดการผู้ใช้</h5>
          <div class="card-body">
            <div class="row row-cols-2">
            @if(Session::get('role') == "roleAdmin")
              <div class="col text-center">
              <a href="/main/Admindata"><img src="https://i.imgur.com/Yb39ZCX.png" title="จัดการเจ้าหน้าที่" class="rounded-circle" style="cursor:pointer;" width="120" height="120">
                <p class="text-left text-form text-center" style="margin-top:10px">จัดการเจ้าหน้าที่</p>
                </a>
              </div>
              @else
              <div class="col text-center" style="display: none">
              <a href="/main/Admindata"><img src="https://i.imgur.com/Yb39ZCX.png" title="จัดการเจ้าหน้าที่" class="rounded-circle" style="cursor:pointer;" width="120" height="120">
                <p class="text-left text-form text-center" style="margin-top:10px">จัดการเจ้าหน้าที่</p>
                </a>
              </div>
              @endif
              <div class="col text-center">
              <a href="/main/Userdata"><img src="https://i.imgur.com/HxE3z2I.png" title="จัดการผู้ใช้ไลน์" class="rounded-circle " style="cursor:pointer;" width="120" height="120">
                  <p class="text-left text-form text-center" style="margin-top:10px;">จัดการผู้ใช้ไลน์</p>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card">
          <div class="card-header" style="background-color: rgb(226, 196, 123);">
          <div class="dropdown ">
          <span style="font-size: 1.25rem;font-weight: 500;line-height: 1.2;">จัดการริชเมนู</span>
          
          @if(count($notify) == 0)
          <button type="button" class="btn btn-secondary p-0 pl-1 pr-1 " id="dropdownNotify" style="float:right;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bell"></i>แจ้งเตือน</button>
          @else
          <button type="button" class="btn btn-primary p-0 pl-1 pr-1 " id="dropdownNotify" style="float:right;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bell"></i> แจ้งเตือน <span class="badge badge-danger">{{count($notify)}}</span></button>
          @endif

    @if(count($notify) == 0)
          <div class="dropdown-menu dropdown-menu-right bg-light" aria-labelledby="dropdownNotify" style="max-width:500px;">
            <p class="dropdown-item text-center">ไม่มีการแจ้งเตือน</p>
          </div>
    @else
          <div class="dropdown-menu dropdown-menu-right bg-light" aria-labelledby="dropdownNotify" style="max-width:500px;">
          @foreach($notify as $item)
            @if($item->status == 'success')
              <a class="dropdown-item alert-success mb-1" style="word-wrap: break-word;white-space: normal;" href="/main/Richdata">{{$item->detail}}<br>
              <p>{{$item->created_at->format('d/m/Y เวลา: H:i:s')}}</p>
              </a>
            @else
              <a class="dropdown-item alert-danger mb-1" style="word-wrap: break-word;white-space: normal;" href="/main/Richdata">{{$item->detail}}<br>
              <p>{{$item->created_at->format('d/m/Y เวลา: H:i:s')}}</p>
              </a>
            @endif
          @endforeach
            <a class="dropdown-item text-center" id="delNotification" href="#">ลบการแจ้งเตือนทั้งหมด</a>
          </div>

    @endif
          
         </div>
          
          
          </div>
          
          <div class="card-body">
            <div class="col text-center">
            <a href="/main/Richdata"><img src="https://i.imgur.com/OsYrKDx.png" title="รายการริชเมนู" class="rounded-circle " style="cursor:pointer;" width="120" height="120">
                <p class="text-left text-form text-center" style="margin-top:10px;">รายการริชเมนู</p>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container" style="margin-top:20px; margin-bottom:40px;">
    <div class="row">
      <div class="col">
      @if(Session::get('role') == "roleAdmin")
        <div class="card">
          <h5 class="card-header" style="background-color: rgb(226, 196, 123);">ตั้งค่า LINE CHATBOT</h5>
          <div class="card-body">
            <div class="col text-center">
            <a href="/changeConfig" title="ตั้งค่าการใช้งาน" data-toggle="modal" data-target="#configModel"><img src="https://i.imgur.com/QK14qN0.png" class="rounded-circle" style="cursor:pointer;" width="120" height="120">
              <!-- <a href="/changeConfig" data-toggle="modal" data-target="#configModel"><img src="https://i.imgur.com/QK14qN0.png" class="rounded-circle" style="cursor:pointer;" width="120" height="120"> -->
                <p class="text-left text-form text-center" style="margin-top:10px">ตั้งค่าการใช้งาน</p>
              </a>
            </div>
          </div>
        </div>
        @elseif(Session::get('role') == "roleStaff")
        <div class="card" style="display: none">
          <h5 class="card-header" style="background-color: rgb(226, 196, 123);">ตั้งค่า LINE CHATBOT</h5>
          <div class="card-body">
            <div class="col text-center">
              <a href="/main/Admindata" data-toggle="modal" data-target="#configModel"><img src="https://i.imgur.com/QK14qN0.png" class="rounded-circle" style="cursor:pointer;" width="120" height="120">
                <p class="text-left text-form text-center" style="margin-top:10px">ตั้งค่าการใช้งาน</p>
              </a>
            </div>
          </div>
        </div>
        @endif
      </div>
      <div class="modal fade" id="configModel" tabindex="-1" role="dialog" aria-labelledby="configModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <form id="formoid" name="formoid">@csrf
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="configModelLabel">ตั้งค่าการใช้งาน</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                <input type="hidden" name="_token" value="{ { csrf_token() } }">
                  <label for="message-text" class="col-form-label">Channel access token:</label>
                  <input class="form-control" id="cat" name="cat" value="{{$at}}"></input>
                </div>
                <div class="form-group">
                  <label for="message-text" class="col-form-label">Channel secret:</label>
                  <input class="form-control" id="cs" name="cs" value="{{$cs}}"></input>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">ยกเลิก</button>
              <button type="button" class="btn btn-primary" id="btn-submit" type="submit">ยืนยัน</button>
            </div>
        </form>
          </div>
        </div>
      </div>
      <div class="col">
      </div>
    </div>
  </div>
</body>

</html>
