<h1>Hallo Users!</h1>

@foreach($users as $user)
    <ul>
        <li>{{$user->name}}</li>
    </ul>
@endforeach
