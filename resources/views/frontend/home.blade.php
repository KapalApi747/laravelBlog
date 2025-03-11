<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel</title>
</head>
<body>
    <h1>Hello Laravel!</h1>
    @can('viewAdminPanel', App\Models\User::class)
        <a href="{{ route('backendindex') }}">Admin Dashboard</a>
    @endcan
</body>
</html>
