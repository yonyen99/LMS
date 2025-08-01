@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="alert alert-info text-center">
        <p>The role details are now accessible via the "View" action on the <a href="{{ route('roles.index') }}">Roles</a> page.</p>
    </div>
</div>
@endsection