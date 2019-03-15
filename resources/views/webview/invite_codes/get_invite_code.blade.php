<!DOCTYPE html>
<html>
<head>
  <title>Cheers</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
{{$inviteCode->code}}
</body>
</html>