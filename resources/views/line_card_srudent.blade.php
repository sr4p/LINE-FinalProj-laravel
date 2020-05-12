<?php

require_once '../vendor/autoload.php';
?>

<!DOCTYPE html>
<html lang="en,{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- <link rel="stylesheet" href="{{asset('css/newcss.css')}}"> -->
      <!-- <link rel="stylesheet" type="text/css" href="{{asset('css/pikaday.css')}}"> -->
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Card</title>
</head>
<body>
    <section class="background-color: #f4f4f4; ">
        <div  style="position: fixed;min-width: 100%;min-height: 90%;">
            <div  style="margin-left: auto;margin-right: auto;">
            
            <div style="">
        <table style="width:50%;">
                <tbody style="background-color: #f4f4f4; "><tr>
                  <td width="25%" rowspan="6" align="center"><img src="https://reg.buu.ac.th/registrar/nurseGetImg.asp?id={{$iddd}}" style="width:130px; padding-top:0px; padding-left:10px; padding-right:10px; display:inline; text-align:left;"><br><font style="  font-size:10px; color:#000093;">บัตรประจำตัวนิสิต</font></td>
                  <td width="75%"><img src="https://buu-oss.buu.ac.th/theme/images/logo_card_1.jpg" style="padding-top:30px;padding-right:10px; display:inline; text-align:center ;vertical-align: top;"></td>
                </tr>
                <tr>
                  <td align="center" style="padding-top: 8px;font-size:14px;">{{$ft}}</td>
                </tr>
                <tr>
                  <td align="center" style="font-size:14px;">{{$fe}}</td>
                </tr>
                <tr>
                  <td align="center"><b><font color="#000" style="font-size:16px;">{{$pt1}}{{$pt2}} {{$pt3}}</font></b></td>
                </tr>
                <tr>
                  <td align="center"><font color="#000" style="font-size:16px;">{{$pe1}}{{$pe2}} {{$pe3}}</font></td>
                </tr>
                <tr>
                  <td align="center"><font color="#000" style="font-size:16px;">{{$iddd}}</font></td>
                </tr>
                <tr>
                  <td align="center" colspan="2"><p style="padding-top:0px;padding-bottom: 0px;"><svg id="barcode" width="178px" height="35px" x="0px" y="0px" viewBox="0 0 178 35" xmlns="http://www.w3.org/2000/svg" version="1.1" style="transform: translate(0px, 0px);"><rect x="0" y="0" width="178" height="35" style="fill:#ffffff;"></rect><g transform="translate(10, 10)" style="fill:#000000;"><rect x="0" y="0" width="4" height="15"></rect><rect x="6" y="0" width="2" height="15"></rect><rect x="12" y="0" width="6" height="15"></rect><rect x="22" y="0" width="6" height="15"></rect><rect x="34" y="0" width="4" height="15"></rect><rect x="40" y="0" width="2" height="15"></rect><rect x="44" y="0" width="2" height="15"></rect><rect x="50" y="0" width="6" height="15"></rect><rect x="58" y="0" width="4" height="15"></rect><rect x="66" y="0" width="4" height="15"></rect><rect x="76" y="0" width="2" height="15"></rect><rect x="82" y="0" width="2" height="15"></rect><rect x="88" y="0" width="6" height="15"></rect><rect x="98" y="0" width="2" height="15"></rect><rect x="104" y="0" width="4" height="15"></rect><rect x="110" y="0" width="6" height="15"></rect><rect x="118" y="0" width="2" height="15"></rect><rect x="124" y="0" width="4" height="15"></rect><rect x="132" y="0" width="4" height="15"></rect><rect x="142" y="0" width="6" height="15"></rect><rect x="150" y="0" width="2" height="15"></rect><rect x="154" y="0" width="4" height="15"></rect></g></svg></p></td>
                </tr></tbody>
        </table>
            </div>
            </div>
		</div>
</section>
</body>
</html>