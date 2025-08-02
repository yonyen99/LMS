@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="alert alert-info text-center">
        <p>The role creation form is now accessible via the "Add Role" button on the <a href="{{ route('roles.index') }}">Roles</a> page.</p>
    </div>
</div>
@endsection