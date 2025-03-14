@extends('layouts.backend')
@section('title', 'Users')
@section('breadcrumb')
    @include('layouts.partials.breadcrumbs')
@endsection
@section('charts')
@endsection
@section('cards')
    @include('layouts.partials.cards')
@endsection
@section('content')
    @yield('cards')
    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Users Beheer</h1>
        @can('create', App\Models\Post::class)
            <a href="{{ route('users.create') }}" class="btn btn-primary">Nieuwe User</a>
        @endcan
    </div>

    <form method="GET" action="{{route('users.index')}}" class="mb-3">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-filter"></i> Filter Users
            </div>
            <div class="card-body">
                <form method="GET" action="{{route('users.index')}}">
                    <div class="row g-3">
                        <!--Zoekveld-->
                        <div class="col-md-4">
                            <label for="search" class="form-label fw-bold">
                                Search by Name
                            </label>
                            <input
                                type="text"
                                name="search"
                                id="search"
                                class="form-control"
                                placeholder="Enter User..."
                                value="{{request('search')}}"
                            >
                        </div>
                        {{--Filter en Reset Knop--}}
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="fas fa-sync-alt"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </form>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All Users
        </div>
        <div class="card-body">
            <p class="text-muted">Showing {{ $users->total() > 0 ? $users->count() : 0 }} of {{ $users->total() }} users</p>
            <table class="table table-striped rounded shadow drop-shadow-2xl">
                <thead>
                <tr>
                    <th>@sortablelink('id', 'Id')</th>
                    <th>Photo</th>
                    <th>@sortablelink('name', 'Name')</th>
                    <th>@sortablelink('email', 'Email')</th>
                    <th>Role</th>
                    <th>@sortablelink('is_active', 'Active')</th>
                    <th>@sortablelink('created_at', 'Created')</th>
                    <th>@sortablelink('updated_at', 'Updated')</th>
                    <th>Deleted</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>Id</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Active</th>
                    <th>Created</th>
                    <th>Updated</th>
                    <th>Deleted</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
                <tbody>
                @if($users)
                    @foreach($users as $user)
                        <tr>
                            <td>{{$user->id}}</td>
                            <td>
                                @if($user->photo && file_exists(public_path('assets/img/' . $user->photo->path)))
                                    <div class="d-flex justify-content-center align-items-center">
                                        <img
                                            src="{{asset('assets/img/' . $user->photo->path)}}"
                                            alt="{{$user->photo->alternate_text ?? $user->name}}"
                                            class="img-fluid rounded object-fit-cover"
                                            style="width: 40px; height: 40px;"
                                        >
                                    </div>

                                @else
                                    <div class="d-flex justify-content-center align-items-center">
                                        <img src="https://placehold.co/40"
                                             alt="No Image"
                                             class="img-fluid rounded object-fit-cover"
                                             style="width: 40px; height: 40px;"
                                        >
                                    </div>
                                @endif
                            </td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>
                                <div>
                                    @foreach($user->roles as $role)
                                        <span class="badge rounded-pill text-bg-primary">
                                            <i class="fas fa-user-shield"></i>
                                            {{$role->name}}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div
                                    class="{{$user->is_active == 1 ? 'badge rounded-pill text-bg-success' : 'badge rounded-pill text-bg-danger'}}">
                                    {{$user->is_active == 1 ? 'Active' : 'Not Active'}}
                                </div>
                            </td>
                            <td>{{$user->created_at->diffForHumans()}}</td>
                            <td>{{$user->updated_at->diffForHumans()}}</td>
                            <td>{{$user->deleted_at ? $user->deleted_at->diffForHumans() : '-'}}</td>
                            <td>
                                <a
                                    href="{{ route('users.edit', $user->id) }}"
                                    class="btn btn-info btn-sm"
                                    title="Edit User"
                                >
                                    <i class="fas fa-edit text-white"></i>
                                </a>
                                @if($user->trashed())
                                    <form
                                        action="{{ route('users.restore', $user->id) }}"
                                        method="POST"
                                        style="display:inline"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="btn btn-success btn-sm"
                                            title="Restore User"
                                        >
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                @else
                                    <form
                                        action="{{ route('users.destroy', $user->id) }}"
                                        method="POST"
                                        style="display:inline"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm"
                                            title="Delete User"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            {!! $users->appends(request()->except('page'))->render() !!}
        </div>
    </div>

@endsection
