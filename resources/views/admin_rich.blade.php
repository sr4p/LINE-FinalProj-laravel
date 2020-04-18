<?php

require_once '../vendor/autoload.php';
?>
<!DOCTYPE html>
<html lang="en,{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="{{asset('css/main.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('css/util.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <!-- <link rel="stylesheet" href="/resources/demos/style.css"> -->
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>ริชเมนู</title>
</head>
<body>
    <script type="text/javascript">

function ready() {
      var getUsername = localStorage.getItem("username");
      document.getElementById('usernameShow').innerHTML = getUsername;
    }
    document.addEventListener("DOMContentLoaded", ready);

    $(document).ready(function() {
        $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $("#btn-submit").click(function(e){
        e.preventDefault();
        var name_rich = $('input[name="name"]').val();
        var json = $('#foo').val();
        var file_data = $('#file').prop('files')[0];

    var form_data = new FormData();
    form_data.append('file', file_data);
    form_data.append('name', name_rich);
    form_data.append('json', json);

        $.ajax({
            type: 'POST',
            url: '/createRichmenu',
            data: form_data,
            contentType: false,
            processData: false,
            success: function(data) {
                // alert('Success')
                window.location.reload();
            }, error: function(data){
                // alert('Fail')
                jQuery('.alert-danger').show();
                jQuery('.alert-danger').append('<li>'+'กรุณาใส่ข้อมูลให้ถูกต้อง'+'</li>');
            }
        });
});

      $("#btn-submit-RM").click(function(e){
        e.preventDefault();

            var rrm1 = $('select[name="rm1"]').val();

            var rrm2 = $('select[name="rm2"]').val();

            var rrm3 = $('select[name="rm3"]').val();
            var time2 = $('input[name="datepicker"]').val();
            var time3 = $('input[name="datepicker2"]').val();



        $.ajax({
            type: 'POST',
            url: '/useRichmenu',
            data: {
                rich_login: rrm1,
                rich_student: rrm2,
                rich_personnal: rrm3,
                time_stu: time2,
                time_per: time3
            },
            success: function(data) {
                window.location.reload();
            }, error: function(data){
                // alert('Fail')
                jQuery('.alert-danger-dt').show();
                jQuery('.alert-danger-dt').append('<li>'+'กรุณาตั้งเวลาให้เป็นอนาคต'+'</li>');
            }
        });
    });

    });


    $( function() {
    $( "#datepicker" ).datepicker({format: 'dd-mm-yyyy'});
  } );

  $( function() {
    $( "#datepicker2" ).datepicker({format: 'dd-mm-yyyy'});
  } );

  function delRich(id){
    $.ajax({
        type: "DELETE",
        url: `/delRichmenu/${id}`,
        data: {
          richId: id
        },
        success: function(res) {
            // window.location.reload();
        },
        error: function(e) {
          console.log('No');
        }
      });
  }

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
    </script>

<nav class="navbar navbar-expand-lg sticky-top navbar-light" style="background-color: rgb(226, 196, 123);">
        <div class="container">
            <a class="navbar-brand" href="/main">LINE CHATBOT BUU</a>
        </div>
        <div class="dropdown">
            <button class="outline-primary my-2 my-sm-0 dropdown-toggle" style="margin-left:55px" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span id="usernameShow"></span>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                <button class="dropdown-item" type="button" onclick="removeUname()">ออกจากระบบ</button>
            </div>
        </div>
        </div>
</nav>

    <div class="container">
    <form class="form-inline">
      <div class="form-group mt-2 mb-2 ml-auto p-2" style="margin-right: 76%">
        <button class="btn btn-success ml-auto" style="margin-right: 20px" type="button" data-toggle="modal" data-target="#addRichModel">เพิ่มริชเมนู <i class="fa fa-plus"></i></button>
        <button class="btn btn-info ml-auto"  type="button" data-toggle="modal" data-target="#useRichModel" >ใช้งานริชเมนู <i class="fa fa-image"></i></button>
      </div>

      @if(Session::has('success'))
    <div class="alert alert-success" style="width:250px;margin-left: 1%">
        {{Session::get('success')}}
    </div>
@endif

@if(Session::has('error'))
    <div class="alert alert-danger" style="width:250px;margin-left: 1%">
        {{Session::get('error')}}
    </div>
@endif

@if(Session::has('msg'))
    <div class="alert alert-danger" style="width:250px;margin-left: 1%">
        {{Session::get('msg')}}
    </div>
@endif

    </form>

    <div class="modal fade" id="addRichModel" tabindex="-1" role="dialog" aria-labelledby="addRichModel" aria-hidden="true">
    
    <form>@csrf
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="alert alert-danger" style="display:none"></div>
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAdminModelLabel">เพิ่มริชเมนู</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row" style="margin-top:20px">
                                <div class="col-md-2 text-right" style="margin-top:5px">ชื่อ</div>
                                <input class="form-control" style="width:220px" type="text" id="name" name="name">
                            </div>

                            <div style="margin-top:20px">
                                <span  style="font-size: 10px;color: red;margin-left : 15%">*ขนาดรูป 2500x1686, 2500x843, 1200x810, 1200x405, 800x540, 800x270</span>
                                <br><span  style="font-size: 10px;color: red;margin-left : 15%">*ได้เฉพาะ PNG</span>
                                <br><span  style="font-size: 10px;color: red;margin-left : 15%">*ความใหญ่ของไฟล์ ไม่เกิน 1MB</span>
                            </div>

                            <div class="row" style="margin-top:20px">
                                <div class="col-md-2 text-right" style="margin-top:5px;margin-bottom:5px">รูป</div>
                                <input type="file" id="file" style="width:220px;" name="file">
                            </div>

                            <div class="row" style="margin-top:20px">
                                <div class="col-md-2 text-right" style="margin-top:5px">โค้ด .json</div>
                                <textarea class="form-control" id="foo" style="width:570px; height:200px" name="foo"></textarea>
                            </div>
                            <div style="margin-top:20px">
                                <span class="" style="font-size: 10px;color: red;margin-left : 15%">*แนะนำ ออกแบบโดยโปรแกรม LINE Bot Designer</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="button" class="btn btn-primary" id="btn-submit" type="submit">เพิ่ม</button>
                    </div>
                </div>
            </div>
    </form>
    </div>

        <div class="modal fade" id="useRichModel"  tabindex="-1" role="dialog" aria-labelledby="useRichModel" data-toggle="modal" aria-hidden="true" >
        
    <form>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="alert alert-danger alert-danger-dt" style="display:none"></div>
                    <div class="modal-header">
                        <h4 class="modal-title" id="useRichModelLabel">ใช้งานริชเมนู</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="container-fluid">
                        <h4><span class="badge badge-primary">เมนูเริ่มต้น</span></h4>
                            <div class="row">
                                <div class="col" style="margin-top:5px">ริชเมนู หน้าเริ่มต้น :</div>
                                @if(empty($rich))
                                <select class="form-control" style="width:200px;margin-right:50px" name="rm1" id="rm1">
                                    <option selected value=""></option>
                                </select>
                                @endif

                                @if(!empty($rich))
                                <select class="form-control" style="width:200px;margin-right:50px" name="rm1" id="rm1">
                                    @foreach($richL as $item)
                                    <option selected value="{{$item->richId}}">{{$item->name}}</option>
                                    @endforeach
                                    @foreach($richDisable as $item)
                                    <option value="{{$item->richId}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                            <hr class="my-4">
                        <h4><span class="badge badge-primary">นิสิต</span></h4>
                            <div class="row" style="margin-top:20px">
                                <div class="col" style="margin-top:5px">ริชเมนู หน้าเข้าสู่ระบบ :</div>
                                @if(empty($rich))
                                <select class="form-control" style="width:200px;margin-right:50px" name="rm2" id="rm2">
                                    <option selected value=""></option>
                                </select>
                                @endif

                                @if(!empty($richS))
                                <select class="form-control" style="width:200px;margin-right:50px" name="rm2" id="rm2">
                                    @foreach($richS as $item)
                                    <option selected value="{{$item->richId}}">{{$item->name}}</option>
                                    @endforeach
                                    @foreach($richDisable as $item)
                                    <option value="{{$item->richId}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                            <div class="row" style="margin-top:20px">
                                <div class="col" style="margin-top:5px">กำหนดเวลาใช้งาน :</div>
                                <input class="form-control" style="width:200px;margin-right:50px" name="datepicker" id="datepicker"></input>
                            </div>
                            <div style="margin-top:5px">
                                <span class="" style="font-size: 10px;color: red;margin-left : 45%">*หากจะใช้งานทันที ไม่ต้องกำหนดเวลา</span>
                            </div>
                            <hr class="my-4">
                            <h4><span class="badge badge-primary">บุคลากร</span></h4>
                            <div class="row" style="margin-top:20px">
                                <div class="col" style="margin-top:5px">ริชเมนู หน้าเข้าสู่ระบบ :</div>
                                @if(empty($rich))
                                <select class="form-control" style="width:200px;margin-right:50px" name="rm3" id="rm3">
                                    <option selected value=""></option>
                                </select>
                                @endif

                                @if(!empty($rich))
                                <select class="form-control" style="width:200px;margin-right:50px" name="rm3" id="rm3">
                                    @foreach($richP as $item)
                                    <option selected value="{{$item->richId}}">{{$item->name}}</option>
                                    @endforeach
                                    @foreach($richDisable as $item)
                                    <option value="{{$item->richId}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                            <div class="row" style="margin-top:20px">
                                <div class="col" style="margin-top:5px">กำหนดเวลาใช้งาน :</div>
                                <input class="form-control" style="width:200px;margin-right:50px" name="datepicker2" id="datepicker2"></input>
                            </div>
                            <div style="margin-top:5px">
                                <span class="" style="font-size: 10px;color: red;margin-left : 45%">*หากจะใช้งานทันที ไม่ต้องกำหนดเวลา</span>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        <button type="button" class="btn btn-primary" id="btn-submit-RM" type='submit'>ใช้งาน</button>
                    </div>
                </div>
            </div>
            </form>
        </div>

<div class="container-fluid">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">ชื่อริชเมนู</th>
                        <th scope="col" class="text-center">การใช้งาน</th>
                        <th scope="col" class="text-center">เครื่องมือ</th>
                    </tr>
                </thead>
                <tbody>
                @if(empty($rich))
                <tr>
                </tr>
                                @else
                                @foreach($rich as $item)
                    <tr>
                    <form>
                        <td class="text-center">{{$item->name}}</td>
                        <td class="text-center">{{$item->status}}</td>
                        <td class="text-center">
                        <input type="hidden" name="id-rich" id="id-rich" value="{{$item->richId}}">
                            <button class="btn btn-danger" id='delete-rich' type="submit" onclick="delRich('{{$item->richId}}')">ลบ <i class="fa fa-trash"></i></button>
                        </td>
                        </form>
                    </tr>
                    @endforeach
                                @endif


                </tbody>
            </table>
        </div>
    </div>

</body>
