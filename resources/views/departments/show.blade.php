@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="alert alert-info text-center">
        <p>The department details are now accessible via the "View" action on the <a href="{{ route('departments.index') }}">Departments</a> page.</p>
    </div>
</div>
@endsection