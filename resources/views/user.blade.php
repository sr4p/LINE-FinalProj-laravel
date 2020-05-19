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
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ข้อมูลผู้ใช้ไลน์</title>
</head>

<body>
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    // function search() {
    //   var input, filter, table, tr, td, i, txtValue;
    //   input = document.getElementById("myInput");
    //   filter = input.value.toUpperCase();
    //   table = document.getElementById("myTable");
    //   tr = table.getElementsByTagName("tr");
    //   for (i = 0; i < tr.length; i++) {
    //     td = tr[i].getElementsByTagName("td")[0];
    //     if (td) {
    //       txtValue = td.textContent || td.innerText;
    //       if (txtValue.toUpperCase().indexOf(filter) > -1) {
    //         tr[i].style.display = "";
    //       } else {
    //         tr[i].style.display = "none";
    //       }
    //     }
    //   }
    // }
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

    function sortTableUname(n) {
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
              $("#sortBtn1").attr('class', 'fa fa-sort-down');
              break;
            }
          } else if (dir == "desc") {
            if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
              shouldSwitch = true;
              $("#sortBtn1").attr('class', 'fa fa-sort-up');
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

    function sortTableTime(n) {
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
          var newSplit = x.innerHTML.split('/').join("-").split(" ")
          var changeFormat = newSplit[0].split('-')
          var setupDate = changeFormat[2] + "-" + changeFormat[1] + "-" + changeFormat[0] + "T" + newSplit[1]
          var newSplit2 = y.innerHTML.split('/').join("-").split(" ")
          var changeFormat2 = newSplit2[0].split('-')
          var setupDate2 = changeFormat2[2] + "-" + changeFormat2[1] + "-" + changeFormat2[0] + "T" + newSplit2[1]
          // console.log(new Date(setupDate))
          //console.log(new Date(newSplit[0]))
          if (dir == "asc") {
            
            if (new Date(setupDate) > new Date(setupDate2)) {
              shouldSwitch = true;
              $("#sortBtn2").attr('class', 'fa fa-sort-down');
              break;
            }
            // array.sort(function(a, b) {
            //   a = new Date(a.dateModified);
            //   b = new Date(b.dateModified);
            //   return a > b ? -1 : a < b ? 1 : 0;
            // });
          } else if (dir == "desc") {
            if (new Date(setupDate) < new Date(setupDate2)) {
              shouldSwitch = true;
              $("#sortBtn2").attr('class', 'fa fa-sort-up');
              break;
            }
          }
        }
        if (shouldSwitch) {
          rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
          //console.log( rows[i])
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

    var idGlobal
    var unGlobal

    function change(un, uid) {
      unGlobal = un;
      idGlobal = uid;
    }

    function changeStatus() {
      var dataSend = {
        un: unGlobal,
        uid: idGlobal
      }
      $.ajax({
        type: "POST",
        url: '/main/Userdata/change',
        data: dataSend,
        success: function(res) {
          location.reload(true);
        },
        error: function(e) {
          console.log(e);
        }
      });
    }

    function showInfo(un) {
      console.log(un)
      $.ajax({
        type: "POST",
        url: '/main/Userdata/modal',
        data: {
          username: un
        },
        success: function(a4) {
          document.getElementById('username').innerHTML = "ชื่อผู้ใช้: " + a4[0].username;
          document.getElementById('nameMD').innerHTML = "ชื่อ: " + a4[0].name_thai + " " + a4[0].surname_thai;
          document.getElementById('branchMD').innerHTML = "วิทยาเขต: " + a4[0].campus_thai;
          document.getElementById('facMD').innerHTML = "คณะ: " + a4[0].faculty_thai;
          if (a4[0].program_thai == undefined && a4[0].level_thai == undefined) {
            document.getElementById('fac1MD').innerHTML = "สาขา: -";
            document.getElementById('levelMD').innerHTML = "ระดับการศึกษา: -";
          } else {
            document.getElementById('fac1MD').innerHTML = "สาขา: " + a4[0].program_thai;
            document.getElementById('levelMD').innerHTML = "ระดับการศึกษา: " + a4[0].level_thai;
          }
          if (a4[0].department_thai == undefined && a4[0].position_thai == undefined) {
            document.getElementById('departMD').innerHTML = "แผนก: -";
            document.getElementById('positionMD').innerHTML = "ตำแหน่ง: -";
          } else {
            document.getElementById('departMD').innerHTML = "แผนก: " + a4[0].department_thai;
            document.getElementById('positionMD').innerHTML = "ตำแหน่ง: " + a4[0].position_thai;
          }
        },
        error: function(e) {
          console.log(e);
        }
      });
    }

    function push(uid) {
      idGlobal = uid;
    }

    function pushMessage() {
      var dataSend = {
        uid: idGlobal,
        msg: document.getElementById("message-text1").value
      }
      $.ajax({
        type: "POST",
        url: 'Userdata/push',
        data: dataSend,
        success: function(res) {
          alert('ส่งข้อความเรียบร้อยแล้ว')
          console.log('success');
        },
        error: function(e) {
          console.log(e);
        }
      });
    }
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
      <!-- <span class="text-form p-b-10 ">ข้อมูลผู้ใช้ไลน์</span> -->
      <span class="text-form font-weight-bold" style="font-size: 21px;">ข้อมูลผู้ใช้ไลน์</span>
      </div>
    </div>
  </div>

  <div class="container">
    <form class="form-inline">
      <div class="form-group mt-2 mb-2 ml-auto p-2" style="margin-right: 7px">
      <input class="form-control mr-sm-1 ml-auto" type="text" placeholder="ค้นหา..." id="myInput">
      </div>
    </form>

    <div class="container-fluid">
    <table class="table table-borderless table-hover">
        <thead>
          <tr style="background-color: rgb(226, 196, 123);">
            <th onclick="sortTableUname(0)" scope="col" class="text-center" style="width:100px; cursor:pointer;">ชื่อผู้ใช้ <i id="sortBtn1" class="fa fa-sort-down"></i></th>
            <th scope="col" class="text-center">รูปภาพไลน์</th>
            <th scope="col" class="text-center" style="width:200px;">ชื่อ - นามสกุล</th>
            <th scope="col" class="text-center" style="width:120px;">ชื่อไลน์</th>
            <th scope="col" class="text-center" style="width:100px;">สถานะ</th>
            <th onclick="sortTableTime(5)" scope="col" class="text-center" style="width:220px; cursor:pointer;">วันและเวลาที่เข้าสู่ระบบ <i id="sortBtn2" class="fa fa-sort-down"></i></th>
            <th scope="col" class="text-center" style="width:250px;">เครื่องมือ</th>
          </tr>
        </thead>
        <tbody id="myTable" >
          @foreach($a2 as $item)
          <tr>
            <td class="text-center">{{$item->username}}</td>
            <td class="text-center"><img src="{{ $item->picture }}" width="90" height="90" /></td>
            <td class="text-center">
              {{$item->prefix_thai}}{{$item->name_thai}} {{$item->surname_thai}}
            </td>
            <td class="text-center">{{$item->displayName}}</td>

            <td class="text-center">{{$item->status}}</td>
            <td class="text-center">{{$item->updated_at->format('d/m/Y H:i:s')}}</td>
            <td class="text-center">
            <button class="btn btn-success rounded-circle" type="button" title="ส่งข้อความ" data-toggle="modal" data-target="#messageModel" onclick="push('{{$item->userId}}')"><i class="fa fa-envelope"></i></button>
              <button class="btn btn-info rounded-circle" type="button" title="ดูข้อมูล" data-toggle="modal" data-target="#infoModel" onclick="showInfo('{{$item->username}}')"><i class="fa fa-info-circle"></i></button>
              <button class="btn btn-danger rounded-circle" type="button" title="ออกจากระบบ" data-toggle="modal" data-target="#changeStModal" onclick="change('{{$item->username}}','{{$item->userId}}')"><i class="fa fa-sign-out"></i></button>
            </td>
          </tr>

          <div class="modal fade" id="changeStModal" tabindex="-1" role="dialog" aria-labelledby="changeStModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
              <div class="modal-header" style="background-color: rgb(226, 196, 123);">
                  <h5 class="modal-title" id="messageModelLabel">ยืนยันการลบ</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                <a>คุณต้องการให้ผู้ใช้คนนี้ออกจากระบบไลน์ใช่หรือไม่</a>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
                  <button type="button" class="btn btn-primary" onclick="changeStatus()" data-dismiss="modal">ยืนยัน</button>
                </div>
              </div>
            </div>
          </div>

          <div class="modal fade" id="messageModel" tabindex="-1" role="dialog" aria-labelledby="messageModelLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
              <div class="modal-header" style="background-color: rgb(226, 196, 123);">
                  <h5 class="modal-title" id="messageModelLabel">ส่งข้อความหาผู้ใช้</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form>
                    <div class="form-group">
                      <label for="message-text" class="col-form-label">ข้อความ:</label>
                      <textarea class="form-control" id="message-text1"></textarea>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
                  <button type="button" class="btn btn-primary" onclick="pushMessage()" data-dismiss="modal">ส่ง</button>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="modal fade" id="infoModel" tabindex="-1" role="dialog" aria-labelledby="infoModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
      <div class="modal-header" style="background-color: rgb(226, 196, 123);">
          <h5 class="modal-title" id="exampleModalLongTitle">ข้อมูลเพิ่มเติม</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <a id="username">ชื่อผู้ใช้: </a><br>
          <a id="nameMD">ชื่อ: </a><br>
          <a id="branchMD">วิทยาเขต: </a><br>
          <a id="facMD">คณะ: </a><br>
          <a id="fac1MD">สาขา: </a><br>
          <a id="levelMD">ระดับการศึกษา: </a><br>
          <a id="departMD">แผนก: </a><br>
          <a id="positionMD">ตำแหน่ง: </a>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
        </div>
      </div>
    </div>
  </div>
</body>

</html>