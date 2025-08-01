@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="alert alert-info text-center">
        <p>The leave type creation form is now accessible via the "Add Leave Type" button on the <a href="{{ route('leave-types.index') }}">Leave Types</a> page.</p>
    </div>
</div>
@endsection