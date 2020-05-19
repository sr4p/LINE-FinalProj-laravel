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
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>ริชเมนู</title>
</head>
<body>
    <script type="text/javascript">

document.addEventListener("DOMContentLoaded", function() {
            var elements = document.getElementsByTagName("INPUT");
            for (var i = 0; i < elements.length; i++) {
                elements[i].oninvalid = function(e) {
                    e.target.setCustomValidity("");
                    if (!e.target.validity.valid) {
                            console.log(e.target)
                        e.target.setCustomValidity("กรุณากรอกข้อมูลในช่องให้ครบถ้วน");
                    }
                };
                elements[i].oninput = function(e) {
                    e.target.setCustomValidity("");
                    // document.getElementById("btn-submit").disabled = false;
                };
            }
        })

        document.addEventListener("DOMContentLoaded", function() {
            var elements = document.getElementsByTagName("textarea");
            for (var i = 0; i < elements.length; i++) {
                elements[i].oninvalid = function(e) {
                    e.target.setCustomValidity("");
                    if (!e.target.validity.valid) {
                            console.log(e.target)
                        e.target.setCustomValidity("กรุณากรอกข้อมูลในช่องให้ครบถ้วน");
                    }
                };
                elements[i].oninput = function(e) {
                    e.target.setCustomValidity("");
                };
            }
        })

   
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



//create rich
var _URL = window.URL;
$("#file").change(function(e) {
  var file, img;

  if ((file = this.files[0])) {
    img = new Image();

    img.onload = function() {
        
    var flagSize = false;
    var flagType = false;
    var flagWH = false;

    var arrErr = [];

    console.log("width : " + this.width + " Height :" + this.height)
    console.log("type : " + file.type)
    console.log("log file : " + file.size)

//image/jpeg
if (file.type == "image/jpeg" || file.type == "image/png") {
        flagType = true;
    } else {
        flagType = false;
        // alert("- อัพโหลดไฟล์รูปภาพเป็น PNG หรือ JPEG เท่านั้น!\n");
        arrErr.push("- อัพโหลดไฟล์รูปภาพเป็น PNG หรือ JPEG เท่านั้น!\n");
        $("#file").val('');

    }

//size
    if (file.size <= 1048576) {
        flagSize = true;
    } else {
        flagSize = false;
        var sizeimg = file.size / 1024;
        arrErr.push(`- ไฟล์นี้ขนาด(${sizeimg.toFixed(0)}KB) ใหญ่เกิน 1024KB ไม่สามารถใช้งานได้ โปรดเลือกไฟล์ใหม่!\n`);
    }

//wh
    if((this.width == 2500 && this.height == 1686) || (this.width == 2500 && this.height == 843) || (this.width == 1200 && this.height == 810)
        || (this.width == 1200 && this.height == 405) || (this.width == 800 && this.height == 540) || (this.width == 800 && this.height == 270)){
            flagWH = true;
    } else {
            flagWH = false;
            arrErr.push(`- ไฟล์ขนาดของรูปภาพนี้(${this.width}x${this.height}) ไม่สามารถใช้งานได้ โปรดเลือกไฟล์ใหม่!\n`);
    }

    if(flagType == true && flagSize == true && flagWH == true){
        // alert('Finish');
    } else {
        if(arrErr.length == 1){
            alert(`${arrErr[0]}`);
        } else if(arrErr.length == 2) {
            alert(`${arrErr[0]}${arrErr[1]}`);
        } else {
            alert(`${arrErr[0]}${arrErr[1]}${arrErr[2]}`);
        }
        
        $("#file").val('');
        
    }


    };
    img.onerror = function() {
        alert("อัพโหลดไฟล์รูปภาพเป็นImage file ( PNG หรือ JPEG ) เท่านั้น!");
        $("#file").val('');
    };

    img.src = _URL.createObjectURL(file);
  }
});


var jsR = document.getElementById("foo");
jsR.onkeyup = function()
{
    console.log("With inValid JSON Text: "+IsValidJSONString($('#foo').val()));
    var flag = IsValidJSONString($('#foo').val());
    if(flag){
        jsR.classList.remove("border");
        jsR.classList.remove("border-danger");
        $( '#jsf1' ).hide();
        document.getElementById("jsonfail1").innerHTML = '';
        flagJson = true;
        return true;
    } else {
        jsR.classList.add("border");
        jsR.classList.add("border-danger");
        $( '#jsf1' ).show();
        document.getElementById("jsonfail1").innerHTML = '*JSON ไม่ถูกต้อง';
        flagJson = false;
        return false;
    }
}

function IsValidJSONString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}



$("#ss").click(function(e){
        event.preventDefault();

        if(flagJson == true){
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
                if(data.error_ceate){
                        $( '#j2' ).show();
                        $( '#j3' ).show();
                        $( '#json2-error' ).html("*"+data.error_ceate.js[0]);
                        
                        var getData = `${data.error_detail}`;
                        var rep = getData.split('{"message":').join('<br/> {"message":');
                        var text = rep.replace('<br/> {"message":','{"message":');
                        document.getElementById("logErr").innerHTML = text;
                        $( '#j4' ).show();
                        $('#foo').css({"border-style":"solid","border-color":"red"});
                } else {
                    
                }

                if($.isEmptyObject(data.error_ceate) && $.isEmptyObject(data.error_detail)){
                    window.location.reload();
                    // alert(data)
                } else {
                //     //
                }
                    // console.log("Finish")
                    // alert("out")
                // }
                }
            });
        } else {
            // validate("foo");
            // var input = document.getElementById("foo");
            // input.setCustomValidity('You gotta fill this out, yo!');
            // alert("JSON : Failed!")
        

            return false;
        }
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
            
            }
        });
    });



    });

    var flagJson = false;
    
    function addRich(){
        event.preventDefault();
        if(flagJson == true){
            var name_rich = $('input[name="name"]').val();
            var json = $('#foo').val();
            var file_data = $('#file').prop('files')[0];

            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('name', name_rich);
            form_data.append('json', json);

            $.ajax({
            type: 'POST',
            headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
            url: '/createRichmenu',
            data: form_data,
            contentType: false,
            processData: false,
            success: function(data) {
                if(data.error_ceate){
                        $( '#j2' ).show();
                        $( '#j3' ).show();
                        $( '#json2-error' ).html("*"+data.error_ceate.js[0]);
                        
                        var getData = `${data.error_detail}`;
                        var rep = getData.split('{"message":').join('<br/> {"message":');
                        var text = rep.replace('<br/> {"message":','{"message":');
                        document.getElementById("logErr").innerHTML = text;
                        $( '#j4' ).show();
                        $('#foo').css({"border-style":"solid","border-color":"red"});
                } else {
                    
                }

                if($.isEmptyObject(data.error_ceate) && $.isEmptyObject(data.error_detail)){
                    window.location.reload();
                    // alert(data)
                    return true;
                } else {
                //     //
                }

                },
                        error: function(e) {
                            console.log(e);
                        }
            });
        } else {
            return false
        }
    }

var count = 0;
$(document).on('click','#switch-success',function(e){
    var status = $('#switch-success:checked').val();
    count = count+1;
        if(count == 1 || count%2 == 1){
            document.getElementById("datepicker").disabled = false;
        $( function() {
            var today = new Date(Date.now() + 24 * 60 * 60 * 1000);
    $( "#datepicker" ).datepicker({ minDate: today,dateFormat: 'dd-mm-yy' });
  } );
        } else {
            document.getElementById("datepicker").value = "";
            document.getElementById("datepicker").disabled = true;
        }
});

var count2 = 0;
$(document).on('click','#switch-success2',function(e){
    var status = $('#switch-success2:checked').val();
    count2 = count2+1;
        if(count2 == 1 || count2%2 == 1){
            document.getElementById("datepicker2").disabled = false;
        $( function() {
            var today = new Date(Date.now() + 24 * 60 * 60 * 1000);
    $( "#datepicker2" ).datepicker({ minDate: today,dateFormat: 'dd-mm-yy' });
  } );
        } else {
            document.getElementById("datepicker2").value = "";
            document.getElementById("datepicker2").disabled = true;
        }
});

    var idrich
    var nameDel

    var Cancelidrich
    var nameCancel

    function cancelR(id,name) {
        Cancelidrich = id;
        nameCancel = name;
        document.getElementById("canR").innerHTML = nameCancel;
    }

    function removeR(id,name) {
        idrich = id;
        nameDel = name;
        document.getElementById("delR").innerHTML = nameDel;
    }

  function delRich(){
      var dataSend = {
          richId: idrich
        }
    $.ajax({
        type: "DELETE",
        url: '/delRichmenu',
        data: dataSend,
        success: function(data) {
            window.location.reload();
        },
        error: function(data) {
          console.log('No');
        }
      });
  }
  

  function CancelRich(){
    var dataSend = {
          richId: Cancelidrich
        }
    $.ajax({
        type: "POST",
        url: '/cancelRichmenu',
        data: dataSend,
        success: function(data) {
            window.location.reload();
        },
        error: function(data) {
          console.log('No');
        }
      });
  }

  function printErrorMsg (msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display','block');
            $.each( msg, function( key, value ) {
                $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
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

    function resetModalAdd(){
        document.getElementById("frmAdd").reset();
    }

    $(document).ready(function() {
      $("#myInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });
    });
    window.onload = function() {
      var getUsername = localStorage.getItem("username");
      document.getElementById('usernameShow').innerHTML = getUsername;
    };


    function validate(inputID) {
  var input = document.getElementById(inputID);
//   var validityState_object = input.validity;
//   if(validityState_object.valueMissing) {
     input.setCustomValidity('You gotta fill this out, yo!');
//   } else if (input.rangeUnderflow) {
//     input.setCustomValidity('We need a higher number!');
//   } else if (input.rangeOverflow) {
//     input.setCustomValidity('Thats too high!');
//   } else {
//     input.setCustomValidity('');
//   }
}

function sortTableSt() {
            var n = 0;
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("myTable");
            switching = true;
            dir = "asc";
            while (switching) {
                switching = false;
                rows = table.rows;
                for (i = 0; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];
                    if (dir == "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            $("#sortBtn1").attr('class', 'fa fa-sort-up');
                            break;
                        }
                    } else if (dir == "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            $("#sortBtn1").attr('class', 'fa fa-sort-down');
                            break;
                        }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                } else {
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }

        $(document).ready(function(){
  $('#tableSort').trigger('click');
});

    </script>

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
      <div class="wrap-login100 p-t-15 p-b-10">
      <!-- <span class="text-form p-b-10 ">รายการริชเมนู</span> -->
      <span class="text-form font-weight-bold" style="font-size: 21px;">รายการริชเมนู</span>
      </div>
    </div>
  </div>

    <div class="container">
    <form class="form-inline">
      <div class="form-group p-3" style="width:100%">
        <button class="btn btn-success" style="margin-right:20px" type="button" data-toggle="modal" data-target="#addRichModel">เพิ่มริชเมนู <i class="fa fa-plus"></i></button>
        <button class="btn btn-info" style="" type="button" data-toggle="modal" data-target="#useRichModel" >ตั้งค่าใช้งานริชเมนู <i class="fa fa-image"></i></button>
        <input class="form-control mr-sm-1 ml-auto" style="float:right"  type="text" placeholder="ค้นหาชื่อ" id="myInput">
      </div>
    </form>
      @if(Session::has('success'))
    <div class="alert alert-success" style="width:97%;margin-left: 1%">
        {{Session::get('success')}}
    </div>
@endif

@if(Session::has('error'))
    <div class="alert alert-danger" style="width:97%;margin-left: 1%">
        {{Session::get('error')}}
    </div>
@endif

@if(Session::has('msg'))
    <div class="alert alert-danger" style="width:97%;margin-left: 1%">
        {{Session::get('msg')}}
    </div>
@endif

    </form>

    <div class="modal fade" id="addRichModel" tabindex="-1" role="dialog" aria-labelledby="addRichModel" aria-hidden="true">

    
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAdminModelLabel">เพิ่มริชเมนู</h5>
                        <button type="button" class="close" onclick="resetModalAdd()" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="frmAdd" onsubmit="return addRich()">@csrf


    <div class="alert alert-info text-left mr-4 ml-4 mt-2">
                  <strong>คำแนะนำ</strong><br>
                    &nbsp;ชื่อริชเมนู<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;- ตั้งชื่อได้ไม่เกิน 20 ตัวอักษร<br>
                    &nbsp;รูปริชเมนู <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;- ขนาดรูป 2500x1686, 2500x843, 1200x810, 1200x405, 800x540 และ 800x270<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;- ต้องเป็น PNG หรือ JPEG เท่านั้น<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;- ความใหญ่ของไฟล์ ไม่เกิน 1 MB(1024KB)<br>
                    &nbsp;โค้ดJSON ริชเมนู<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;- แนะนำ ออกแบบโดยโปรแกรม LINE Bot Designer
                    <br>
    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row" style="">
                                <div class="col-md-2 text-right" style="">ชื่อริชเมนู</div>
                                <input class="form-control" style="width:250px" type="text" id="name" name="name" maxlength="20" required>
                                <div class="valid-feedback">
                                </div>
                            </div>
                            <!-- <div class="row mt-2">
                                <div class="col-md-2 text-right"></div>
                                &nbsp;<a style="color:red" id="name-error"></a>
                            </div> -->

                            <div class="row" style="margin-top:20px">
                                <div class="col-md-2 text-right" style="margin-top:5px;margin-bottom:5px;">รูปริชเมนู</div>
                                <input class="form-control-file" type="file" id="file" style="width:250px;" name="file" required>
                            </div>

                            <!-- <div class="row mb-3" id="f1"> -->
                                <!-- <div class="col-md-2 text-right" style=""></div>
                                &nbsp;<a style="color:red" id="file1-error" class=""></a>
                            </div>

                            <div class="row mb-3" style="display:none" id="f2">
                                <div class="col-md-2 text-right" style=""></div>
                                &nbsp;<a style="color:red" id="size-pic" class=""></a>
                            </div> -->

                            <div class="row" style="margin-top:20px">
                                <div class="col-md-2 text-right" style="margin-top:20px">โค้ด JSON</div>
                                <textarea class="form-control" id="foo" style="width:600px; height:200px" name="foo" required></textarea>
                            </div>

                            <div class="row mt-2" style="display:none" id="jsf1">
                                <div class="col-md-2 text-right"></div>
                                &nbsp;<a style="color:red" id="jsonfail1"></a>
                            </div>


                            <div class="row mt-2" style="display:none" id="j1">
                                <div class="col-md-2 text-right"></div>
                                &nbsp;<a style="color:red" id="json1-error"></a>
                            </div>

                            <div class="row mt-2" style="display:none" id="j2">
                                <div class="col-md-2 text-right"></div>
                                &nbsp;<a style="color:red" id="json2-error"></a>
                            </div>

                            <div class="row mt-2" style="display:none" id="j3">
                                <div class="col-md-2 text-right"></div>
                                <!-- &nbsp;<a style="color:red">*คลิกที่นี่เพื่อดู error detail </a> -->
                                <!-- <br><button type="button" class="btn btn-outline-danger btn-sm" ><i class="fas fa-file-code"></i>คลิกเพื่อดูส่วนที่ผิดพลาด</button> -->
                                <button class="btn btn-warning btn-sm" type="button" data-toggle="collapse" data-target="#collapseLogError" aria-expanded="false" aria-controls="collapseExample">คลิกเพื่อดูส่วนที่ผิดพลาด</button>
                            </div>

                            <div class="row mt-2" style="display:none" id="j4">
                                <div class="col-md-2 text-right"></div>
                                <div class="collapse" style="width:550px;" id="collapseLogError">
                                    <div class="card card-body" ><a id="logErr"></a></div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" onclick="resetModalAdd()" data-dismiss="modal">ปิด</button>
                        <button class="btn btn-primary" type="submit" id="btn-submit">เพิ่ม</button>
                    </div>
                    </form>
                </div>
            </div>
    
    </div>

        <div class="modal fade" id="useRichModel"  tabindex="-1" role="dialog" aria-labelledby="useRichModel" data-toggle="modal" aria-hidden="true" >

    <form id="frmUse">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="useRichModelLabel">ตั้งค่าใช้งานริชเมนู</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="alert alert-danger print-error-use" style="display:none">
        <ul></ul>
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
                                <div class="col" style="margin-top:5px mr-1">ต้องการกำหนดเวลา : </div>
                                <div class="col" style="margin-top:5px">
                                <div class="custom-control custom-switch" >
                                    <input type="checkbox" class="custom-control-input"  id="switch-success" name="switch-success">
                                    <label class="custom-control-label" for="switch-success"></label>
                                </div></div>
                            </div>
                            

                            <div class="row" style="margin-top:20px">
                                <div class="col" style="margin-top:5px">เวลาใช้งาน :</div>
                                <input class="form-control" style="width:200px;margin-right:50px" type="text" name="datepicker" id="datepicker" disabled>
                            </div>

<!-- // -->
                            <div class="row mr-3" >
                                <div class="col text-right ">
                                &nbsp;<a style="color:red" id="date-error1"></a>
                                </div>
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
                                <div class="col" style="margin-top:5px mr-1">ต้องการกำหนดเวลา : </div>
                                <div class="col" style="margin-top:5px">
                                <div class="custom-control custom-switch" >
                                    <input type="checkbox" class="custom-control-input"  id="switch-success2" name="switch-success2">
                                    <label class="custom-control-label" for="switch-success2"></label>
                                </div></div>
                            </div>

                            <div class="row" style="margin-top:20px">
                                <div class="col" style="margin-top:5px">เวลาใช้งาน :</div>
                                <input class="form-control" style="width:200px;margin-right:50px" name="datepicker2" id="datepicker2" disabled></input>
                            </div>

                            <div class="row mr-3" >
                                <div class="col text-right ">
                                &nbsp;<a style="color:red" id="date-error2"></a>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
                        <button type="button" class="btn btn-primary" id="btn-submit-RM" type='submit'>ใช้งาน</button>
                    </div>
                </div>
            </div>
            </form>
        </div>

        <div class="modal fade" id="removeStModal" tabindex="-1" role="dialog" aria-labelledby="removeStModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
              <div class="modal-header" style="background-color: rgb(226, 196, 123);">
                  <h5 class="modal-title" id="messageModelLabel">ยืนยันการลบ</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <a>คุณต้องการยืนยันที่จะลบริชเมนูชื่อ <a id='delR'></a> ใช่หรือไม่</a>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
                  <button type="button" class="btn btn-primary" onclick="delRich()" data-dismiss="modal">ยืนยัน</button>
                </div>
              </div>
            </div>
        </div>

        <div class="modal fade" id="CancelStModal" tabindex="-1" role="dialog" aria-labelledby="CancelStModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
              <div class="modal-header" style="background-color: rgb(226, 196, 123);">
                  <h5 class="modal-title" id="messageModelLabel">ยืนยันการยกเลิกการกำหนดเวลาใช้งานริชเมนู</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <a>คุณต้องการยืนยันที่จะยกเลิกการกำหนดเวลาใช้งานริชเมนู <a id='canR'></a> ใช่หรือไม่</a>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
                  <button type="button" class="btn btn-primary" onclick="CancelRich()" data-dismiss="modal">ยืนยัน</button>
                </div>
              </div>
            </div>
        </div>

<div class="container-fluid" >
            <table class="table table-borderless table-hover" id="">
                <thead>
                    <tr style="background-color: rgb(226, 196, 123);">
                        <th scope="col" class="text-center" style="width:34%">ชื่อริชเมนู</th>
                        <th onclick="sortTableSt()" id="tableSort" scope="col" class="text-center" style="width:33%;cursor:pointer;">การใช้งาน <i id="sortBtn1" class="fa fa-sort-down"></th>
                        <th scope="col" class="text-center" style="width:33%">เครื่องมือ</th>
                    </tr>
                </thead>
                <tbody id="myTable">
                @if(empty($rich))
                <tr>
                </tr>
                                @else
                                @foreach($rich as $item)
                    <tr>
                    <form>
                        
                        <td class="text-center">{{$item->name}}</td>

                        @if($item->status == 'นิสิต' || $item->status == 'บุคลากร' || $item->status == 'เมนูเริ่มต้น')
                            <td class="text-center">กำลังใช้งาน<br/>({{$item->status}})</td>
                        @elseif($item->status == 'กำหนดเวลาใช้งาน')
                            <td class="text-center">{{$item->status}} {{$item->timeRich}}<br/>({{$item->timeType}})</td>
                        @else
                            <td class="text-center">{{$item->status}}</td>
                        @endif

                        <td class="text-center">
                        <input type="hidden" name="id-rich" id="id-rich" value="{{$item->richId}}">
                        @if($item->status == 'ยังไม่ได้ใช้งาน')
                            <button class="btn btn-danger rounded-circle" title="ลบ" id='delete-rich' type="button" data-toggle="modal" data-target="#removeStModal" onclick="removeR('{{$item->richId}}','{{$item->name}}')"><i class="fa fa-trash"></i></button>
                        @elseif($item->status == 'นิสิต' || $item->status == 'บุคลากร' || $item->status == 'เมนูเริ่มต้น')
                            <button class="btn btn-dark rounded-circle" title="ลบ" id='delete-rich' type="button" data-toggle="modal" data-target="#removeStModal" onclick="removeR('{{$item->richId}}','{{$item->name}}')" disabled><i class="fa fa-trash"></i></button>
                        @else
                            <button class="btn btn-warning rounded-circle" title="ยกเลิก" id='cancel-rich' type="button" data-toggle="modal" data-target="#CancelStModal" onclick="cancelR('{{$item->richId}}','{{$item->name}}')"><i class="fa fa-ban"></i></button>
                        @endif

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
