@extends('layouts.backend')
@section('title')
    Create A User
@endsection
@section('charts')
@endsection
@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-center">
            @include('layouts.partials.form_error')
        </div>
        <div class="col-4 offset-4 mt-5">
            <form action="{{action('\App\Http\Controllers\UserController@store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{old('name')}}"
                    >
                    @error('name')
                    <p class="text-danger text-sm">{{$message}}</p>
                    @enderror
                </div>
                <div class="form-group mt-3">
                    <label for="email">Email:</label>
                    <input
                        type="text"
                        name="email"
                        class="form-control"
                        value="{{old('email')}}"
                    >
                </div>
                <div class="form-group mt-3">
                    <label for="role_id">Select Role: (ctrl + click to select multiple)</label>
                    <select class="form-select" name="role_id[]" id="role_id" multiple>
                        <option value="" disabled>Select Role</option>
                        @foreach($roles as $id => $role)
                            <option value="{{$id}}">{{$role}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mt-3">
                    <label for="is_active">Select Status:</label>
                    <select class="form-select" name="is_active" id="is_active">
                        <option value="1" {{old('is_active') == "1" ? 'selected' : ""}}>Active</option>
                        <option value="0" {{old('is_active') == "0" ? 'selected' : ""}}>Not Active</option>
                    </select>
                </div>
                <div class="form-group mt-3">
                    <label for="password">Password:</label>
                    <input
                        type="text"
                        name="password"
                        class="form-control"
                        value="{{old('password')}}"
                    >
                </div>
                <div class="form-group mt-3">
                    <label for="photo_id">Image:</label>
                    <input
                        type="file"
                        name="photo_id"
                        id="photo_id"
                        class="form-control"
                    >
                </div>
                <div class="form-group mt-5 d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
