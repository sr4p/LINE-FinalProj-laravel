<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Service</title>

    <script src="https://static.line-scdn.net/liff/edge/2.0/sdk.js"></script>

    <script>
        function getId(){
            liff.init({liffId:"1653845388-jBLvL5n1"}, () => {}, err => console.error(err.code, error.message));
        liff.getProfile().then(profile => {
            document.getElementById('u1').value = profile.userId;
            document.card.submit();
    }).catch((err) => {
      console.log('error', err);
        });
    }
    </script>

</head>
<body onload="getId()">
    <div>
        <form name="card" action="/service/studentcard" method="post">
        @csrf
            <input type="hidden" name="u1" id="u1">
        </form>

    </div>
</body>
</html>