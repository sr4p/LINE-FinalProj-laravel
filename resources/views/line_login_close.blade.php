<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Login</title>

   <script charset="utf-8" src="https://static.line-scdn.net/liff/edge/2.0/sdk.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        function getId(){
          liff.init({liffId:"1653845388-2mPZP8OR"}, () => {liff.closeWindow()}, err => console.error(err.code, error.message));
          
    }
    </script>
  </head>
<body onload="getId()">
@csrf
    <div>
    </div>
</body>
</html>
