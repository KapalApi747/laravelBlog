<h1>Hallo Posts!</h1>

@foreach($posts as $post)
    <ul>
        <li>{{$post->user->name}}</li>
        <h2>
            {{$post->title}}
        </h2>
        <p>
            {{$post->body}}
        </p>
    </ul>
@endforeach
