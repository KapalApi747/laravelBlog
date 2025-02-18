@extends('layouts.backend')
@section('title')
    Users
@endsection
@section('charts')
@endsection
@section('content')
    @if(session('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            All Users
        </div>
        <div class="card-body">
            <table class="table table-striped rounded shadow drop-shadow-2xl">
                <thead>
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
            <div>
                {{$users->links()}}
            </div>
        </div>
    </div>

@endsection
