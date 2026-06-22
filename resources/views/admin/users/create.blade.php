@extends('layouts.admin')

@section('content')
<div class="container">
      <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Manage Users</h1>
            <div class="actions">
                <a href="{{ route('admin.users.create') }}"
                    class="btn btn-success mb-3">
                    Create User
                </a>
            </div>
     </div>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control">
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="is_verified" value="1" id="is_verified" class="form-check-input" checked>
            <label for="is_verified" class="form-check-label">Verified user</label>
        </div>

        <button class="btn btn-primary">
            Create User
        </button>
    </form>
</div>
@endsection
