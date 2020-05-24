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
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>ข้อมูลเจ้าหน้าที่</title>
</head>

<body>
    <script>
        // function search() {
        //     var input, filter, table, tr, td, i, txtValue;
        //     input = document.getElementById("myInput");
        //     filter = input.value.toUpperCase();
        //     table = document.getElementById("myTable");
        //     tr = table.getElementsByTagName("tr");
        //     for (i = 0; i < tr.length; i++) {
        //         td = tr[i].getElementsByTagName("td")[0];
        //         if (td) {
        //             txtValue = td.textContent || td.innerText;
        //             if (txtValue.toUpperCase().indexOf(filter) > -1) {
        //                 tr[i].style.display = "";
        //             } else {
        //                 tr[i].style.display = "none";
        //             }
        //         }
        //     }
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

        function del() {
            $.ajax({
                type: "GET",
                url: '/main/Admindata/change',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    window.location.href = '/main/Admindata'
                }
            });
        }

        function sortTableUn(n) {
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
                            $("#sortBtn").attr('class', 'fa fa-sort-up');
                            break;
                        }
                    } else if (dir == "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            $("#sortBtn").attr('class', 'fa fa-sort-down');
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

        function sortTableSt(n) {
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

        function telPhone(event) {
            var element = document.getElementById("tel");
            var element2 = document.getElementById("tel2");
            var phoneno = /^\d{10}$/;
            if (phoneno.test(event.target.value)) {
                element.classList.remove("border");
                element.classList.remove("border-danger");
                element2.classList.remove("border");
                element2.classList.remove("border-danger");
                flagTel = true
                flagTel2 = true
                return true;
            } else {
                element.classList.add("border");
                element.classList.add("border-danger");
                element2.classList.add("border");
                element2.classList.add("border-danger");
                flagTel = false
                flagTel2 = false
                return false;
            }
        }
        var flagEmail = false;
        var flagEmail2 = false;
        var flagTel= false;
        var flagTel2 = false;

        function addUser() {
            event.preventDefault();
            var checkType = document.getElementById('selectRole').value
            var type = ""
            if (checkType == "admin") {
                type = "แอดมิน"
            } else {
                type = "เจ้าหน้าที่"
            }
            
            if (flagEmail && flagTel) {
                var dataSend = {
                    username: document.getElementById('username').value,
                    type: type,
                    name_thai: document.getElementById('firstName').value,
                    surname_thai: document.getElementById('lastName').value,
                    email: document.getElementById('email').value,
                    tel: document.getElementById('tel').value,
                    status: document.getElementById('selectStatus').value
                }
                if (dataSend != null) {
                    $.ajax({
                        type: "POST",
                        url: 'admin/create',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: dataSend,
                        success: function(res) {
                            if (res == 'success') {
                                document.getElementById('username').value = ""
                                document.getElementById('firstName').value = ""
                                document.getElementById('lastName').value = ""
                                document.getElementById('email').value = ""
                                document.getElementById('tel').value = ""
                                location.reload();
                                return true;
                            }
                        },
                        error: function(e) {
                            console.log(e);
                        }
                    });
                }
            } else {
                return false
            }
        }

        function ValidateEmail(mail) {
            var element = document.getElementById("email");
            var element2 = document.getElementById("email2");
            if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mail.target.value)) {
                element.classList.remove("border");
                element.classList.remove("border-danger");
                element2.classList.remove("border");
                element2.classList.remove("border-danger");
                flagEmail = true
                flagEmail2 = true
            } else {
                element.classList.add("border");
                element.classList.add("border-danger");
                element2.classList.add("border");
                element2.classList.add("border-danger");
                flagEmail = false
                flagEmail2 = false
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            var elements = document.getElementsByTagName("INPUT");
            for (var i = 0; i < elements.length; i++) {
                elements[i].oninvalid = function(e) {
                    e.target.setCustomValidity("");
                    if (!e.target.validity.valid) {
                        e.target.setCustomValidity("กรุณากรอกข้อมูลในช่องให้ครบถ้วน");
                    }
                };
                elements[i].oninput = function(e) {
                    e.target.setCustomValidity("");
                };
            }
        })

        function editInfo(un) {
            $.ajax({
                type: "POST",
                url: 'Admindata/editModal',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    username: un
                },
                success: function(a4) {
                    document.getElementById('username2').value = a4[0].username;
                    document.getElementById('firstName2').value = a4[0].name_thai;
                    document.getElementById('lastName2').value = a4[0].surname_thai;
                    document.getElementById('tel2').value = a4[0].tel;
                    document.getElementById('email2').value = a4[0].email;
                    if(a4[0].status == "ใช้งานอยู่"){
                        document.getElementById('selectStatus2').selectedIndex = 0;
                    }else{
                        document.getElementById('selectStatus2').selectedIndex = 1;
                    }
                    if(a4[0].type == "แอดมิน"){
                        document.getElementById('selectRole2').selectedIndex = 0;
                    }else{
                        document.getElementById('selectRole2').selectedIndex = 1;
                    }
                },
                error: function(e) {
                    console.log(e);
                }
            });
        }
        function showInfo(un) {
            $.ajax({
                type: "POST",
                url: 'Admindata/showModal',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    username: un
                },
                success: function(a5) {
                    document.getElementById('username3').innerHTML = "ชื่อผู้ใช้: " + a5[0].username;
                    document.getElementById('name3').innerHTML = "ชื่อ: " + a5[0].name_thai + " " + a5[0].surname_thai;
                    document.getElementById('tel3').innerHTML = "เบอร์โทรศัพท์: " + a5[0].tel;
                    document.getElementById('email3').innerHTML = "อีเมล: " + a5[0].email;
                    document.getElementById('type3').innerHTML = "ประเภท: " + a5[0].type;
                },
                error: function(e) {
                    console.log(e);
                }
            });
        }
        function editUser() {
            event.preventDefault();
            var checkType = document.getElementById('selectRole2').value
            var type = ""
            if (checkType == "admin") {
                type = "แอดมิน"
            } else {
                type = "เจ้าหน้าที่"
            }
            
            if (flagEmail2 && flagTel2) {
                var dataSend = {
                    username: document.getElementById('username2').value,
                    type: type,
                    name_thai: document.getElementById('firstName2').value,
                    surname_thai: document.getElementById('lastName2').value,
                    email: document.getElementById('email2').value,
                    tel: document.getElementById('tel2').value,
                    status: document.getElementById('selectStatus2').value
                }

                $.ajax({
                    type: "POST",
                    url: 'admin/update',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: dataSend,
                    success: function(res) {
                        if (res == 'success') {
                            document.getElementById('username').value = ""
                            document.getElementById('firstName').value = ""
                            document.getElementById('lastName').value = ""
                            document.getElementById('email').value = ""
                            document.getElementById('tel').value = ""
                            location.reload();
                        }
                    },
                    error: function(e) {
                        console.log(e);
                    }
                });
            }
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
                <!-- <span class="text-form p-b-10">ข้อมูลเจ้าหน้าที่</span> -->
                <span class="text-form font-weight-bold" style="font-size: 21px;">ข้อมูลเจ้าหน้าที่</span>
            </div>
        </div>
  </div>

    <div class="container">
        <form class="form-inline">
            <div class="form-group mt-2 mb-2 ml-auto p-2" style="margin-right: 7px">
            <input class="form-control mr-sm-1 ml-auto" type="text" placeholder="ค้นหา..." id="myInput">
            <button class="btn btn-success ml-auto rounded-circle" title="เพิ่มเจ้าหน้าที่" type="button" data-toggle="modal" data-target="#addAdminModel"><i class="fa fa-plus"></i></button>    
            </div>
        </form>
        <div class="modal fade" id="addAdminModel" tabindex="-1" role="dialog" aria-labelledby="addAdminModelLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(226, 196, 123);">
                        <h5 class="modal-title" id="addAdminModelLabel">เพิ่มเจ้าหน้าที่</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form onsubmit="return addUser()">
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-2 text-right" style="margin-top:5px">ประเภท</div>
                                    <select id="selectRole" class="browser-default custom-select" style="width:100px" name="selectRole">
                                        <option selected value="admin">แอดมิน</option>
                                        <option value="officer">เจ้าหน้าที่</option>
                                    </select>
                                    <div class="col-md-2 text-right" style="margin-top:5px">สถานะ</div>
                                    <select id="selectStatus" class="browser-default custom-select" style="width:120px" name="status">
                                        <option selected value="ใช้งานอยู่">ใช้งานอยู่</option>
                                        <option value="ไม่ได้ใช้งาน">ไม่ได้ใช้งาน</option>
                                    </select>
                                </div>
                                <div class="row" style="margin-top:20px">
                                    <div class="col-md-2 text-right" style="margin-top:5px">ชื่อ</div>
                                    <input id="firstName" class="form-control" style="width:220px" name="firstName" required>
                                    <div class="valid-feedback">
                                        
                                    </div>
                                    <div class="col-md-2 text-right" style="margin-top:5px">นามสกุล</div>
                                    <input id="lastName" class="form-control" style="width:220px" name="lastName" required>
                                </div>
                                <div class="row" style="margin-top:20px">
                                    <div class="col-md-2 text-right" style="margin-top:5px">เบอร์โทรศัพท์</div>
                                    <input class="form-control" style="width:220px" name="tel" id="tel" maxlength="10" onkeyup="telPhone(event)"required>
                                    <div class="col-md-2 text-right" style="margin-top:5px">อีเมล</div>
                                    <input id="email" class="form-control" style="width:220px" name="email" onkeyup="ValidateEmail(event)" required>
                                </div>
                                <div class="row" style="margin-top:20px">
                                    <div class="col-md-2 text-right" style="margin-top:5px">ชื่อผู้ใช้</div>
                                    <input id="username" class="form-control" style="width:220px" name="username" required>
                                </div>
                                <p style="margin-left:110px; color:gray;">(ต้องเป็นชื่อผู้ใช้ของมหาวิทยาลัยบูรพาเท่านั้น)</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
                            <button class="btn btn-primary" type='submit'>เพิ่ม</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="container-fluid">
        <table class="table table-borderless table-hover borderless pl-0 pr-0">
                <thead>
                    <tr style="background-color: rgb(226, 196, 123);">
                        <th onclick="sortTableUn(0)" scope="col" class="text-center" style="width:100px; cursor:pointer;">ชื่อผู้ใช้ <i id="sortBtn" class="fa fa-sort-down"></i></th>
                        <th scope="col" class="text-center">ชื่อ-นามสกุล</th>
                        <th onclick="sortTableSt(0)" scope="col" class="text-center" style="cursor:pointer;">สถานะ <i id="sortBtn1" class="fa fa-sort-down"></th>
                        <th scope="col" class="text-center">ประเภท</th>
                        <th scope="col" class="text-center">วันและเวลาที่เข้าใช้งานล่าสุด</th>
                        <th scope="col" class="text-center">เครื่องมือ</th>
                    </tr>
                </thead>
                <tbody id="myTable">
                    @foreach($a3 as $item)
                    <tr>
                        <td class="text-center">{{$item->username}}</td>
                        <td class="text-center">
                            {{$item->prefix_thai}}{{$item->name_thai}} {{$item->surname_thai}}
                        </td>
                        <td class="text-center">{{$item->status}}</td>
                        <td class="text-center">{{$item->type}}</td>
                        <td class="text-center">{{$item->updated_at->format('d/m/Y H:i:s')}}</td>
                        <td class="text-center">
                            <button class="btn btn-info rounded-circle" type="button" title="ดูข้อมูล" data-toggle="modal" data-target="#showInfoModel" onclick="showInfo('{{$item->username}}')"><i class="fa fa-info-circle"></i></button>
                            <button class="btn btn-warning rounded-circle" type="button" title="แก้ไขข้อมูล" data-toggle="modal" data-target="#editModel" onclick="editInfo('{{$item->username}}')"><i class="fa fa-edit"></i></button>
                            <!-- <a href="/main/Admindata/change"><button class="btn btn-danger" type="button">ลบ</button></a> -->
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <div class="modal fade" id="showInfoModel" tabindex="-1" role="dialog" aria-labelledby="showModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(226, 196, 123);">
                    <h5 class="modal-title" id="exampleModalLongTitle">ข้อมูลเพิ่มเติม</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <a id="username3">ชื่อผู้ใช้: </a><br>
                    <a id="name3">ชื่อ: </a><br>
                    <a id="tel3">เบอร์โทรศัพท์: </a><br>
                    <a id="email3">อีเมล: </a><br>
                    <a id="type3">ประเภท: </a><br>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="editModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(226, 196, 123);">
                    <h5 class="modal-title" id="exampleModalLongTitle">แก้ไขข้อมูล</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form onsubmit="return editUser()">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-2 text-right" style="margin-top:5px">ประเภท</div>
                                <select id="selectRole2" class="browser-default custom-select" style="width:100px" name="role">
                                    <option selected value="admin">แอดมิน</option>
                                    <option value="officer">เจ้าหน้าที่</option>
                                </select>
                                <div class="col-md-2 text-right" style="margin-top:5px">สถานะ</div>
                                <select id="selectStatus2" class="browser-default custom-select" style="width:120px" name="status">
                                    <option selected value="ใช้งานอยู่">ใช้งานอยู่</option>
                                    <option value="ไม่ได้ใช้งาน">ไม่ได้ใช้งาน</option>
                                </select>
                            </div>
                            <div class="row" style="margin-top:20px">
                                <div class="col-md-2 text-right" style="margin-top:5px">ชื่อ</div>
                                <input id="firstName2" class="form-control" style="width:220px" name="first_name"></input>
                                <div class="col-md-2 text-right" style="margin-top:5px">นามสกุล</div>
                                <input id="lastName2" class="form-control" style="width:220px" name="last_name"></input>
                            </div>
                            <div class="row" style="margin-top:20px">
                                <div class="col-md-2 text-right" style="margin-top:5px">เบอร์โทรศัพท์</div>
                                <input class="form-control" style="width:220px" name="tel" id="tel2" onkeyup="telPhone(event)" maxlength="10">
                                <div class="col-md-2 text-right" style="margin-top:5px">อีเมล</div>
                                <input id="email2" class="form-control" style="width:220px" name="email" onkeyup="ValidateEmail(event)"></input>
                            </div>
                            <div class="row" style="margin-top:20px">
                                <div class="col-md-2 text-right" style="margin-top:5px">ชื่อผู้ใช้</div>
                                <input id="username2" class="form-control" style="width:220px" name="ad_user" readonly></input>
                            </div>
                        </div>
                    </div>
                
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">ปิด</button>
                        <button class="btn btn-primary" type='submit' value="addAdmin">ยืนยัน</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>